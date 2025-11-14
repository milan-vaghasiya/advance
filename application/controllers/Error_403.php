<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Error_403 extends CI_controller
{
  public function __construct(){
    parent::__construct();
    $this->data['headData'] = new stdClass();
    $this->data['headData']->pageTitle = "Error 403";
  }

  public function index(){
    $this->output->set_status_header('403');
    $this->load->view('page-403',$this->data);
  }
}
?>