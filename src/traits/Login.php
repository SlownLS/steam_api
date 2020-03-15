<?php

namespace SlownLS\Steam\Traits;

use SlownLS\Steam\Exception;

trait Login{
    /**
     * Check if user is logged
     *
     * @return boolean
     */
    protected function IsLogged() : bool
    {
        return isset($_SESSION["user"]);
    }

    /**
     * Check if user is returned by Steam
     *
     * @return boolean
     */
    protected function IsReturned() : bool
    {
        return isset($_GET["openid_mode"]) && !empty($_GET["openid_mode"]);        
    }
    
    /**
     * Check if connection is validate
     *
     * @return bool
     */
    protected function Validate() : Bool
    {
        $params = [];

        foreach ($this->verifyWhitelist as $k => $v) {
            if( isset($_GET[$v]) ){
                $params[$k] = $_GET[$v];
            }
        }

        $params["openid.mode"] = "check_authentication";    
        
        if ( !isset($params["openid.return_to"]) || $params["openid.return_to"] != $this->redirectParams["openid.return_to"] ){
            return false;
        }        

        $client = $this->Client();

        $response = $client->request("POST", $this->urlAuthorize, [ 
            "form_params" => $params, 
            "headers" => [ "Accept" => "application/json" ] 
        ]);

        $response = (string) $response->getBody();        

        if( strpos($response, "is_valid:true") === false ){
            return false;
        }            

        return true;
    }

    /**
     * Redirect user to page login
     *
     * @return void
     */
    protected function Login()
    {
        $this->redirectParams["openid.realm"] = $this->config["realm"];
        $this->redirectParams["openid.return_to"] = $this->config["return_to"];

        $params = http_build_query($this->redirectParams);

        $url = $this->urlAuthorize . "?$params";

        header("Location: $url");
        exit();
    }

    /**
     * Authentificate user to session
     *
     * @return void
     */
    protected function Auth()
    {
        if( !$this->Validate() ){
            throw new Exception("The connection could not be validated");
        }

        $search = $_GET["openid_identity"];
        $steamid = substr($search, strrpos($search, '/') + 1);
        
        $infos = $this->GetInfos($steamid);
        
        $infos = $this->ParseData($infos);

        $_SESSION["user"] = $infos;

        $url = $this->redirectParams["openid.return_to"];

        header("Location: $url");
        exit();        
    }

    /**
     * Logout User
     *
     * @return void
     */
    public function Logout()
    {
        unset($_SESSION["user"]);
    }    
}