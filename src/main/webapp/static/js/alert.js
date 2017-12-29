/*
 * Jquery Message插件
 * 使用例子 :
 * $.alert("提示内容",{
 *      title : "标题",
 *      position : ['left', [-0.1,0]]
 * })
 * 也可以这样使用
 * $.alert("提示内容","标题");
 * 位置请使用
 * top-left,top-right,bottom-left,bottom-right,center 大小写都可以哦
 */

(function ($) {
    $.alert_ext = {
        // 默认配置
        defaults: {
            autoClose: true,  // Enable auto close
            closeTime: 3000,  // Auto close delay time in ms (>1000)
            withTime: false,  // Display a countdown timer
            type: 'danger',   // danger, success, warning, info
            position: ['top-right', [10, 10]], // position+offset : top-left,top-right,bottom-left,bottom-right,center
            title: false, // Message title
            close: '',    // For close button
            speed: 'normal',
            isOnly: true, // Set to false to display multiple messages at a time
            minTop: 0,   // Minimal space in PX from top
            onShow: function () {
            },  // onShow callback
            onClose: function () {
            }  // onClose callback
        },

        // 提示框模版
        tmpl: '<div class="alert alert-dismissable ${State}"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><h4 style="white-space: nowrap;">${Title}</h4><p>${Content}</p></div>',

        // 初始化函数
        init: function (msg, options) {
            this.options = $.extend({}, this.defaults, options);

            this.create(msg);
            this.set_css();

            this.bind_event();

            return this.alertDiv;
        },

        template: function (tmpl, data) {
            $.each(data, function (k, v) {
                tmpl = tmpl.replace('${' + k + '}', v);
            });
            return $(tmpl);
        },

        // 创建提示框
        create: function (msg) {
            this.alertDiv = this.template(this.tmpl, {
                State: 'alert-' + this.options.type,
                Title: this.options.title,
                Content: msg
            }).hide();
            if (!this.options.title) {
                $('h4', this.alertDiv).remove();
                $('p', this.alertDiv).css('margin-right', '15px');
            }
            if (this.options.isOnly) {
                $('body > .alert').remove();
            }
            this.alertDiv.appendTo($('body'));
        },

        // 设置样式
        set_css: function () {
            let alertDiv = this.alertDiv;

            // 初始化样式
            alertDiv.css({
                'position': 'fixed',
                'z-index': 10001 + $(".alert").length
            });

            // IE6兼容
            let ie6 = 0;
            if ($.browser && $.browser.msie && $.browser.version === '6.0') {
                alertDiv.css('position', 'absolute');
                ie6 = $(window).scrollTop();
            }

            // 位置设置提取
            let position = this.options.position,
                pos_str = position[0].split('-'),
                pos = [0, 0];
            if (position.length > 1) {
                pos = position[1];
            }

            // 偏移百分比检测
            if (pos[0] > -1 && pos[0] < 1) {
                pos[0] = pos[0] * $(window).height();
            }
            if (pos[1] > -1 && pos[1] < 1) {
                pos[1] = pos[1] * $(window).width();
            }

            // 位置设置
            for (let i in pos_str) {
                if ($.type(pos_str[i]) !== 'string') {
                    continue;
                }
                let str = pos_str[i].toLowerCase();

                if ($.inArray(str, ['left', 'right']) > -1) {
                    alertDiv.css(str, pos[1]);
                } else if ($.inArray(str, ['top', 'bottom']) > -1) {
                    alertDiv.css(str, pos[0] + ie6);
                } else {
                    alertDiv.css({
                        'top': ($(window).height() - alertDiv.outerHeight()) / 2 + pos[0] + ie6,
                        'left': ($(window).width() - alertDiv.outerWidth()) / 2 + pos[1]
                    });
                }
            }

            if (parseInt(alertDiv.css('top')) < this.options.minTop) {
                alertDiv.css('top', this.options.minTop);
            }
        },

        // 绑定事件
        bind_event: function () {
            this.bind_show();
            this.bind_close();

            if ($.browser && $.browser.msie && $.browser.version === '6.0') {
                this.bind_scroll();
            }
        },

        // 显示事件
        bind_show: function () {
            let ops = this.options;
            this.alertDiv.fadeIn(ops.speed, function () {
                ops.onShow($(this));
            });
        },

        // 关闭事件
        bind_close: function () {
            let alertDiv = this.alertDiv,
                ops = this.options,
                closeBtn = $('.close', alertDiv).add($(this.options.close, alertDiv));

            closeBtn.bind('click', function (e) {
                alertDiv.fadeOut(ops.speed, function () {
                    $(this).remove();
                    ops.onClose($(this));
                });
                e.stopPropagation();
            });

            // 自动关闭绑定
            if (this.options.autoClose) {
                let time = parseInt(this.options.closeTime / 1000);
                if (this.options.withTime) {
                    $('p', alertDiv).append('<span>...<em>' + time + '</em></span>');
                }
                let timer = setInterval(function () {
                    $('em', alertDiv).text(--time);
                    if (!time) {
                        clearInterval(timer);
                        closeBtn.trigger('click');
                    }
                }, 1000);
            }
        },

        // IE6滚动跟踪
        bind_scroll: function () {
            let alertDiv = this.alertDiv,
                top = alertDiv.offset().top - $(window).scrollTop();
            $(window).scroll(function () {
                alertDiv.css("top", top + $(window).scrollTop());
            })
        },

        // 检测是否为手机浏览器
        check_mobile: function () {
            let userAgent = navigator.userAgent;
            let keywords = ['Android', 'iPhone', 'iPod', 'iPad', 'Windows Phone', 'MQQBrowser'];
            for (let i in keywords) {
                if (userAgent.indexOf(keywords[i]) > -1) {
                    return keywords[i];
                }
            }
            return false;
        }
    };

    $.alert = function (msg, arg) {
        if ($.alert_ext.check_mobile()) {
            alert(msg);
            return;
        }
        if (!$.trim(msg).length) {
            return false;
        }
        if ($.type(arg) === 'string') {
            arg = {
                title: arg
            }
        }
        if (arg && arg.type === 'error') {
            arg.type = 'danger';
        }
        return $.alert_ext.init(msg, arg);
    }
})(jQuery);
