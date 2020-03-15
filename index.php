<?php
    require "vendor/autoload.php";

    use SlownLS\Steam\User;

    $config = [
        "apiKey" => "xxxxxxxxxxxx",
        "realm" => "http://127.0.0.1/steam_api/",
        "return_to" => "http://127.0.0.1/steam_api/"
    ];

    User::SetConfig($config);
    
    if( isset($_GET["login"]) ){
        User::Login();
        exit();
    }    

    if( isset($_GET["logout"]) ){
        User::Logout();
        header("Location: " . $_SERVER["PHP_SELF"]);
        exit();        
    }    

    if( User::IsReturned() ){
        try{
            User::Auth();
        }catch(SlownLS\Steam\Exception $e){
            header("Location: ./");
            exit();
        }
    }

    $user = "slownls";

    if( isset($_GET["user"]) && !empty($_GET["user"]) ){
        $user = htmlspecialchars($_GET["user"]);
    }

    try{
        $infos = User::GetInfos($user);
    }catch(SlownLS\Steam\Exception $e){
        echo $e->getMessage();
        exit();
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>SlownLS | Steam API</title>

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.5.13/css/mdb.min.css" rel="stylesheet">       

        <style> body{ background-color: #1b2838; color: #fff; } .navbar { background-color: #171a21; color: white; height: 80px; margin-bottom: 15px; } a{ color: #b8b6b4; transition: all 0.2s linear; } a:hover{ color: white; } .navbar .navbar-brand, .navbar .navbar-brand:hover{ color: white; border: none; } .btn-primary{ background-color: #392E5C !important; } hr{ background-color: #b8b6b4; } </style>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg mb-5">
            <div class="container">
                <a class="navbar-brand" href="./">SlownLS | Steam API</a>
                
                <div class="collapse navbar-collapse" id="navbar">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="./">Home</a>
                        </li>
                    </ul>

                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <?php if( User::IsLogged() ): ?>
                                <a class="nav-link" href="./?logout"><?= User::GetLocalInfo("personaname") ?> (Logout)</a>
                            <?php else: ?>
                                <a class="nav-link" href="./?login">Login</a>
                            <?php endif; ?>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container">
            <div class="text-center mb-5">
                <h1>Welcome to Steam API</h1>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <form action="">
                        <div class="form-group w-100">
                            <input type="text" class="form-control w-100" name="user" placeholder="Enter a Pseudo, SteamID64, custom URL or complete URL..." value="<?= isset($_GET["user"]) ? $_GET["user"] : "" ?>">
                        </div>
                    </form>            
                </div>

                <div class="col-md-12">
                    <div class="float-left mr-2">
                        <img src="<?= $infos->avatarfull ?>" alt="">
                    </div>

                    <p>
                        Pseudo : <?= $infos->personaname ?> <br>
                        SteamID64 : <?= $infos->steamid ?> <br>
                        Profile URL : <a href="<?= $infos->profileurl ?>" target="_blank"><?= $infos->profileurl ?></a> <br>
                        Visibility : <?= $infos->communityvisibilitystate ?> <br>
                        Real name : <?= $infos->realname ?> <br>
                        Status : <?= $infos->personastate ?> <br>
                        Country : <?= $infos->loccountrycode ?> <br>
                        Game : <?= $infos->gameextrainfo ?> 
                    </p>
                </div>
            </div>
        </div>
                            
        <footer>
            <div class="container">
                <div class="text-center">
                    <hr>
                    <p>&copy; 2020 Your Web Site | Created by <a href="https://slownls.fr" target="_blank">SlownLS</a></p>
                </div>
            </div>
        </footer>
    </body>
</html>