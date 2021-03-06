<?php
include_once '../init.php';

class Route
{
    public function __construct(){}

    public  function route(){

        $ctrl_name = Application::filter_input_('controller','');
        $ctrl_action = Application::filter_input_('action','');


        if(empty($ctrl_name))
            $ctrl_name = 'LoginController';

        if(file_exists('controllers/'.$ctrl_name.'.php')){
            include_once 'controllers/'.$ctrl_name.'.php';

            /////////////////////////////////////////////
            // Dynamic controller creation
            $ctrl = new $ctrl_name();

            if(empty($ctrl_action))
                $ctrl_action = 'default';

            $action_name = 'action_'.$ctrl_action;

            if (method_exists($ctrl, $action_name)) {
                $ctrl->$action_name();
            } else {
                Application::error404();
                return;
            }
        }else{
            Application::error404();
            return;
        }
    }
}