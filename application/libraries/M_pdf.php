<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_pdf {

	public function __construct()
    {
        $CI = & get_instance();
    }
    public function load($param = "'utf-8','A4-P','','','5','5','5','5'")
    {
        include_once APPPATH.'/third_party/mpdf/mpdf.php';
        
		return new mPDF();
    }
}  

?>