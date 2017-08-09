<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */
namespace OmegaCommerce\Api;

class Iframe
{
    public function __construct(
        \OmegaCommerce\Api\Config $config,
        \OmegaCommerce\Api\Auth $auth
    )
    {
        $this->config = $config;
        $this->auth = $auth;
    }

    /**
     * @return string
     */
    public function getApplicationUrl()
    {
        $params = array();
        $params["version"] = $this->config->getVersion();
        $params["extID"] = $this->config->getExtID();

        return $this->config->getBaseApiUrl()."?".http_build_query($params);
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if ($this->auth->isAuthorized()) {
            $data = array(
                'token' => $this->auth->getToken(),
                'ID' => $this->config->getID()
            );
        } else {
            $data = $this->auth->register(get_site_url());
        }

        $json = json_encode($data);

        return <<<HTML
<style>
    #update-nag, .update-nag {
        display: none;
    }
    .notice {
        display: none;
    }
    iframe {
        margin-left: -20px;
        margin-top: -10px;
        border: none;
        width: 100%;
        box-sizing: border-box
        z-index: 1000000;
        min-height: 800px;
        height: 100%;
    }
    iframe:focus {
        outline: none;
    }
    iframe[seamless] {
        display: block;
    }
</style>
<iframe id="omega-application-iframe" src="{$this->getApplicationUrl()}"></iframe>
<!--height: calc(100% - 56px);-->
<!--margin-top: 56px-->
<script>
    jQuery(window).load(function(){
        var iframe = document.getElementById('omega-application-iframe').contentWindow;
        var obj = {$json};
        iframe.postMessage(JSON.stringify({key: 'omega-app-storage', data: obj}), "*");
    });
    window.addEventListener('message', function (e) {
        var payload = e.data;
        if (payload == 'reload') {
            window.location.reload();
        }
    }, false);
</script>
HTML;

    }
}