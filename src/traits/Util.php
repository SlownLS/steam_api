<?php

namespace SlownLS\Steam\Traits;

use GuzzleHttp\Client;

trait Util{
    /**
     * Instance of class
     *
     * @var Class
     */
    private static $instance = null;

    /**
     * Redirect OpenID params
     *
     * @var array
     */    
    private $redirectParams = [
        "openid.ns" => "http://specs.openid.net/auth/2.0",
        "openid.mode" => "checkid_setup",
        "openid.identity" => "http://specs.openid.net/auth/2.0/identifier_select",
        "openid.claimed_id" => "http://specs.openid.net/auth/2.0/identifier_select"
    ];

    /**
     * Withlist url params
     *
     * @var array
     */
    private $verifyWhitelist = [
        "openid.ns" => "openid_ns",
        "openid.op_endpoint" => "openid_op_endpoint",
        "openid.claimed_id" => "openid_claimed_id",
        "openid.identity" => "openid_identity",
        "openid.return_to" => "openid_return_to",
        "openid.response_nonce" => "openid_response_nonce",
        "openid.assoc_handle" => "openid_assoc_handle",
        "openid.signed" => "openid_signed",
        "openid.sig" => "openid_sig",
    ];    

    /**
     * Url authorize of Steam API
     *
     * @var string
     */
    public $urlAuthorize = "https://steamcommunity.com/openid/login";

    /**
     * Url to get user infos of Steam
     *
     * @var string
     */
    public $urlUserInfos = "https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=%s&steamids=%s";
    
    /**
     * Url to vanity url Steam
     *
     * @var string
     */
    public $urlVanity = "http://api.steampowered.com/ISteamUser/ResolveVanityURL/v0001/?key=%s&vanityurl=%s";

    /**
     * Url to get friends on steam
     *
     * @var string
     */
    public $urlFriends = "http://api.steampowered.com/ISteamUser/GetFriendList/v0001/?key=%s&steamid=%s&relationship=friend";
   
    /**
     * Url to get friends on steam
     *
     * @var string
     */
    public $urlGames = "http://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key=%s&steamid=%s&format=json";

    /**
     * Guzzle Client
     *
     * @var GuzzleHttp\Client
     */
    private $client;

    /**
     * Configuration variable
     *
     * @var array
     */
    private $config = [];

    /**
     * Set config to twitch api
     *
     * @param array $config
     * @return void
     */
    protected function SetConfig(array $config)
    {
        $this->config = $config;

        $this->redirectParams["openid.realm"] = $config["realm"];
        $this->redirectParams["openid.return_to"] = $config["return_to"];        
    }

    /**
     * Get guzzle client
     *
     * @return GuzzleHttp\Client
     */
    protected function Client()
    {
        if( \is_null($this->client) ){
            $this->client = new Client([
                "http_errors" => false                
            ]);
        }

        return $this->client;
    }       
    
    /**
     * Get instance of user
     *
     * @return Class
     */
    protected static function GetInstance()
    {
        if( \is_null(self::$instance) ){
            self::$instance = new self();
        }
  
        return self::$instance;
    }     

    /**
     * Parse Guzzle response
     *
     * @param [type] $response
     * @return object
     */
    protected function Parse($response) : object
    {
        return \json_decode( (string) $response->getBody() );
    }  

    /**
     * Parse data user infos
     *
     * @param object $data
     * @return object
     */
    protected function ParseData(object $data) : object
    {
        $visibilitystate = "Not defined";
        $profilestate = "Not configured";
        $personastate = "Not defined";

        // communityvisibilitystate
        switch($data->communityvisibilitystate){
            case 1:
                $visibilitystate = "Private";
                break;
            case 2:
                $visibilitystate = "Friend Only";
                break;
            case 3:
                $visibilitystate = "Public";
                break;
        }

        // profilestate
        $data->communityvisibilitystate = $visibilitystate;
        
        if( $data->profilestate == 1 ){
            $profilestate = "Configured";
        }

        $data->profilestate = $profilestate;

        // personastate
        switch($data->personastate){
            case 0:
                $personastate = "Offline";
                break;
            case 1:
                $personastate = "Online";
                break;
            case 2:
                $personastate = "Busy";
                break;
            case 3:
                $personastate = "Away";
                break;
            case 4:
                $personastate = "Snooze";
                break;
            case 5:
                $personastate = "Looking to trade";
                break;
            case 6:
                $personastate = "Looking to play";
                break;
        }

        $data->personastate = $personastate;

        // loccountrycode
        if( !isset($data->loccountrycode) ){
            $data->loccountrycode = "Not defined";
        }
        
        // gameextrainfo
        if( !isset($data->gameextrainfo) ){
            $data->gameextrainfo = "No game running";
        }

        // realname 
        if( !isset($data->realname) ){
            $data->realname = "No real name given";
        }

        return $data;
    }

    /**
     * Check is a valid steamid
     *
     * @param string $steamid
     * @return boolean
     */
    protected function IsSteamID(string $steamid) : bool
    {
        $ptn = "/^(7[0-9]{15,25}+)$/";
        preg_match($ptn, $steamid, $matches);
        
        return !empty($matches);    
    }

    public static function __callStatic($name, $arguments)
    {
        $instance = self::GetInstance();
        
        if( method_exists($instance, $name) ){
            return call_user_func_array( array($instance, $name), $arguments);
        }
    }      
}