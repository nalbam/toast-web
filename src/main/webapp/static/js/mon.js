// Line chart
let ctx_cpu = $("#chart_cpu");
let ctx_hdd = $("#chart_hdd");
let ctx_mon = $("#chart_mon");

let chart_cpu = null;
let chart_hdd = null;
let chart_mon = null;

//Chart.defaults.global.legend = {
//    enabled: false
//};

function load_chart(no, h) {
    let url = '/server/mon/' + no + '?h=' + h;
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function (res, status) {
            console.log('server_mon (' + no + ') : ' + status);

            if (res && res.labels) {
                if (chart_cpu === null) {
                    chart_cpu = new Chart(ctx_cpu, {
                        type: 'line',
                        data: {
                            labels: res.labels,
                            datasets: [{
                                label: "usage",
                                backgroundColor: "rgba(38,185,154,0.31)",
                                borderColor: "rgba(38,185,154,0.7)",
                                pointBorderColor: "rgba(38,185,154,0.7)",
                                pointBackgroundColor: "rgba(38,185,154,0.7)",
                                pointHoverBorderColor: "rgba(220,220,220,1)",
                                pointHoverBackgroundColor: "#fff",
                                pointBorderWidth: 1,
                                pointRadius: 1,
                                data: res.cpu
                            }]
                        },
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true,
                                        suggestedMax: 10
                                    }
                                }]
                            }
                        }
                    });
                } else {
                    chart_cpu.data.labels = res.labels;
                    chart_cpu.data.datasets[0].data = res.cpu;
                    chart_cpu.update();
                }

                if (chart_hdd === null) {
                    chart_hdd = new Chart(ctx_hdd, {
                        type: 'line',
                        data: {
                            labels: res.labels,
                            datasets: [{
                                label: "usage",
                                backgroundColor: "rgba(38,185,154,0.31)",
                                borderColor: "rgba(38,185,154,0.7)",
                                pointBorderColor: "rgba(38,185,154,0.7)",
                                pointBackgroundColor: "rgba(38,185,154,0.7)",
                                pointHoverBorderColor: "rgba(220,220,220,1)",
                                pointHoverBackgroundColor: "#fff",
                                pointBorderWidth: 1,
                                pointRadius: 1,
                                data: res.hdd
                            }]
                        },
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true,
                                        suggestedMax: 100
                                    }
                                }]
                            }
                        }
                    });
                } else {
                    chart_hdd.data.labels = res.labels;
                    chart_hdd.data.datasets[0].data = res.hdd;
                    chart_hdd.update();
                }

                if (chart_mon === null) {
                    chart_mon = new Chart(ctx_mon, {
                        type: 'line',
                        data: {
                            labels: res.labels,
                            datasets: [{
                                label: "load 5",
                                backgroundColor: "rgba(38,185,154,0.31)",
                                borderColor: "rgba(38,185,154,0.7)",
                                pointBorderColor: "rgba(38,185,154,0.7)",
                                pointBackgroundColor: "rgba(38,185,154,0.7)",
                                pointHoverBorderColor: "rgba(220,220,220,1)",
                                pointHoverBackgroundColor: "#fff",
                                pointBorderWidth: 1,
                                pointRadius: 1,
                                data: res.load5
                            }, {
                                label: "load 15",
                                backgroundColor: "rgba(3,88,106,0.3)",
                                borderColor: "rgba(3,88,106,0.70)",
                                pointBorderColor: "rgba(3,88,106,0.70)",
                                pointBackgroundColor: "rgba(3,88,106,0.70)",
                                pointHoverBorderColor: "rgba(151,187,205,1)",
                                pointHoverBackgroundColor: "#fff",
                                pointBorderWidth: 1,
                                pointRadius: 1,
                                data: res.load15
                            }]
                        },
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true,
                                        suggestedMax: 0.2
                                    }
                                }]
                            }
                        }
                    });
                } else {
                    chart_mon.data.labels = res.labels;
                    chart_mon.data.datasets[0].data = res.load5;
                    chart_mon.data.datasets[1].data = res.load15;
                    chart_mon.update();
                }
            }
        }
    });
}
