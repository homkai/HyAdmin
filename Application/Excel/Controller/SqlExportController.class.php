<?php
namespace Excel\Controller;
use Common\Controller\HyAllController;
class SqlExportController extends HyAllController {
    public function export(){
        $this->grade=M('class')->where(array('status'=>1))->group('grade')->getField('grade',true);
        $this->display();
    }

}