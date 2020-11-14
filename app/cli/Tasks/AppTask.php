<?php

declare(strict_types=1);

namespace Harpya\CLI\Tasks;

use \Phalcon\Cli\Task;
use \Harpya\IP\Models\Application;

class AppTask extends Task
{
    public function mainAction()
    {
        echo "\n Help ";
        echo "\n  ";
        echo "\n harpya app <command> <parms>";
        echo "\n Where: ";
        echo "\n    <command> is the operation";
        echo "\n    <parms> ";
        echo "\n\n  Commands ";
        echo "\n  add = create a new application entry";
        echo "\n  list = list all applications configured";
        echo "\n  inspect = give details of a given application";

        echo "\n\n";
    }

    public function addAction($appID = '', ...$options)
    {
        $allowedOptions = [
            'url_authorize' => "Application's URL invoked when user is properly authenticated, to initiate a valid session.",
            'url_after_login' => "Application's URL which browser is redirected after user have been authenticated and authorized properly.",
            'name' => 'Descriptive name of this Application',
            'ip_address' => "Application's IP address (range). Used to be whitelisted and have access to Harpya Services."
        ];

        if (!$appID) {
            echo "\n Help ";
            echo "\n  ";
            echo "\n harpya app add <app-id>";
            echo "\n\n";
            foreach ($allowedOptions as $option => $description) {
                echo sprintf("\n --%-15s : %s ", $option, $description);
            }
            echo "\n\n";
            echo "\n Examples: ";
            echo "\n harpya app add app01 --name='My app 01' --ip_address='10.20.50.35/24,106.130.125.176' ";
            echo "\n\n";
            return;
        }

        $informedOptions = [];

        for ($i = 0;$i < count($options);$i++) {
            //foreach ($options as $k => $option ) {
            $option = $options[$i];

            if (\strpos($option, '=') !== false) {
                list($optKey, $optValue) = explode('=', $option);
            } else {
                $optKey = $option;
                if (!isset($options[$i + 1])) {
                    die("\nMissing value for option $option \n");
                }
                $optValue = $options[$i + 1];
                $i++;
            }

            $optSearch = substr($optKey, 2);
            if (isset($allowedOptions[$optSearch])) {
                $informedOptions[$optSearch] = $optValue;
            } else {
                die("\nInvalid option $option \n");
            }
        }

        $app = new Application();

        foreach ($informedOptions as $optKey => $optValue) {
            $app->$optKey = $optValue;
        }

        $secret = Application::generateSecret();

        $app->secret_hash = Application::getSecretHash($appID, $secret);
        $app->app_id = $appID;

        $failed = true;

        try {
            $resp = $app->save();
            $msgs = $app->getMessages();
            if ($msgs) {
                $resp = $msgs[0]->getMessage();
            } else {
                $failed = false;
            }
        } catch (\Exception $ex) {
            if (\strpos($ex->getMessage(), 'idx_apps') !== false) {
                $resp = "Application with id $appID already exists.";
            } else {
                $resp = $ex->getMessage();
            }
        }

        if ($failed) {
            echo "\nOperation failed. \nReason: $resp\n";
            exit;
        }

        echo "\nSuccess!\nYour secret token is $secret . Copy it and keep it safe, since will not be possible recover later.";
        echo "\n\n";
    }

    public function listAction()
    {
        $lsStatus = [0 => 'inactive', 1 => 'active'];

        $list = Application::find();

        echo "\napp_id         | status    | name";
        echo "\n---------------+-----------+-----------------------------";
        foreach ($list as $item) {
            $appID = $item->app_id;
            if (strlen($appID) > 16) {
                $appID = substr($appID, 0, 13) . '...';
            }

            $status = $lsStatus[$item->status] ?? 'undefined';

            $line = sprintf('%-15s| %-10s| %s', $appID, $status, $item->name);
            echo "\n$line";
            // echo "\n name=" . $item->name;
        }
        echo "\n\n";
    }

    public function inspectAction($appID = null)
    {
        if (!$appID) {
            echo "\n Help ";
            echo "\n  ";
            echo "\n harpya app inspect <app-id>";
            echo "\n\n";
            return;
        }

        $lsStatus = [0 => 'inactive', 1 => 'active'];

        $record = Application::findFirst([
            'app_id = :appID: ',
            'bind' => [
                'appID' => $appID
            ]
        ]);

        if (is_null($record)) {
            echo "Application $appID not found\n\n";
            return;
        }

        $arr = $record->jsonSerialize();

        echo "\n ID.........: " . $arr['app_id'];
        echo "\n Name.......: " . $arr['name'];
        echo "\n Status.....: " . ($arr['status'] ? 'active' : 'inactive');
        echo "\n IP.........: " . ($arr['ip_address'] ? $arr['ip_address'] : '*');
        echo "\n URL Auth...: " . $arr['url_authorize'];
        echo "\n URL After..: " . $arr['url_after_login'] ;
        echo "\n Created at.: " . $arr['created_at'] ;

        echo "\n\n";
    }
}
