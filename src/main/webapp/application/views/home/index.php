<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>toast.sh</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Install</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <pre>
curl -s toast.sh/install | bash

~/toaster/toast.sh auto {fleet} {phase} {org} {token}</pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>AWS User data</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <pre>
#!/bin/bash

runuser -l {user} -c 'curl -s toast.sh/install | bash'

runuser -l {user} -c '~/toaster/toast.sh auto {fleet} {phase} {org} {token}'</pre>
                    </div>
                    <div class="x_content">
                        <pre>
cat /var/log/cloud-init-output.log</pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Usage</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <pre>
~/toaster/toast.sh auto
~/toaster/toast.sh init java
~/toaster/toast.sh deploy fleet
~/toaster/toast.sh deploy target {target}
~/toaster/toast.sh bucket {target}</pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Remote</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <pre>
~/toaster/remote.sh {user} {host} {port} auto
~/toaster/remote.sh {user} {host} {port} init java
~/toaster/remote.sh {user} {host} {port} deploy fleet
~/toaster/remote.sh {user} {host} {port} deploy target {target}
~/toaster/remote.sh {user} {host} {port} bucket {target}</pre>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- /page content -->
