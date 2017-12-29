<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Help</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Default nginx proxy_pass</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <pre>
        location / {
            proxy_pass http://SERVER;
        }</pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>nginx lb - upstream</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <p>1. upstream 은 도메인 중 . (dot) 을 _ (under-bar) 로 치환 하여 설정됨</p>
                        <pre>
    upstream dev-demo_nalbam_com {
        server 10.0.0.1:80 max_fails=3 fail_timeout=10s;
        server 10.0.0.2:80 max_fails=3 fail_timeout=10s;
    }</pre>
                        <p>2. proxy_pass 의 'SERVER' 는 upstream 으로 치환됨</p>
                        <pre>
    server {
        listen        80;
        server_name   dev-demo.nalbam.com;

        location / {
            proxy_pass http://dev-demo_nalbam_com;
        }

        ...
    }</pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>nginx lb - server</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <p>1. 기본 도메인 (dev-demo.nalbam.com)</p>
                        <pre>
    server {
        listen        80;
        server_name   dev-demo.nalbam.com;

        location / {
            proxy_pass http://dev-demo_nalbam_com;
        }

        ...
    }</pre>
                        <p>1-1. 'https redirect' 가 체크 되어있을 경우, 기본 도메인 (dev-demo.nalbam.com) 은 redirect 됨</p>
                        <pre>
    server {
        listen        80;
        server_name   dev-demo.nalbam.com;

        return 301 https://dev-demo.nalbam.com$request_uri;

        ...
    }</pre>
                        <p>2. nalbam.com 도메인은 nalbam-in.com 이 추가 되고, 'default' 또는 'custom location' 적용</p>
                        <pre>
    server {
        listen        80;
        server_name   dev-demo.nalbam-in.com;

        location / {
            proxy_pass http://dev-demo_nalbam_com;
        }

        ...
    }</pre>
                        <p>3. 'https port' 와 'certificate' 가 설정 되어있을 경우, 'default' 또는 'custom location' 적용</p>
                        <pre>
    server {
        listen        443;
        server_name   dev-demo.nalbam.com;

        location / {
            proxy_pass http://dev-demo_nalbam_com;
        }

        ssl on;
        ssl_protocols           SSLv3 TLSv1 TLSv1.1 TLSv1.2;
        ...
    }</pre>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- /page content -->
