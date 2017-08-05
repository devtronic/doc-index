<?php

if (!is_file('user.txt')) {
    die('Please create user.txt and put your GitHub username in it');
}
$username = trim(file_get_contents('user.txt'));

$url = 'https://api.github.com/users/' . $username . '/repos';

$docsDir = 'docs';
$ttl = 3600;
$cacheFile = 'repos.json';
if (!is_file($cacheFile) || time() - filemtime($cacheFile) > $ttl) {
    if (is_file($cacheFile)) {
        unlink($cacheFile);
    }
    file_put_contents($cacheFile, getCurl($url));
}
$repos = json_decode(file_get_contents($cacheFile), true);

echo <<<HTML
<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <title>Documentation Index</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        <style>body{background-color: #eee;}</style>
    </head>
        <body>
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <h1 class="page-header">
                        Documentation Index
                        <small class="pull-right">
                            <a class="btn btn-primary" href="https://github.com/{$username}/">
                                <i class="fa fa-2x fa-github"></i> <span style="font-size:22px;vertical-align: top">{$username}</span>
                            </a>
                        </small>
                    </h1>
                    <div class="list-group">
HTML;
foreach ($repos as $repo) {
    if (is_dir($docsDir . '/' . $repo['full_name'])) {
        echo <<<HTML
                        <div class="list-group-item">
                            <a href="{$docsDir}/{$repo['full_name']}/{$repo['default_branch']}">{$repo['full_name']}</a>
                            <div class="pull-right" style="display: inline">
                                <span class="badge">{$repo['stargazers_count']} <i class="fa fa-star"></i></span>
                                <span class="badge">{$repo['forks_count']} <i class="fa fa-code-fork"></i></span>
                                <a class="btn btn-xs btn-default" href="{$repo['html_url']}" target="_blank">
                                    <i class="fa fa-github"></i>
                                </a>
                            </div>
                        </div>
HTML;
    }
}
echo <<<HTML
                    </div>
                    <div class="text-right">
                        Created with <a href="https://github.com/Devtronic/doc-index" target="_blank">Devtronic/doc-index</a>
                    </div>
                </div>
            </div> 
        </div> 
    </body>
</html>
HTML;

function getCurl($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'DocIndexer');
    $contents = curl_exec($ch);
    curl_close($ch);
    return $contents;
}
