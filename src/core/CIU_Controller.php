<?php

if (! defined('BASEPATH')) {
	exit('No direct script access');
}

if (class_exists('MY_Controller')) {
    class CIU_CI_or_MY_Controller extends MY_Controller {}
} else {
    class CIU_CI_or_MY_Controller extends CI_Controller {}
}

class CIU_Controller extends CIU_CI_or_MY_Controller {

	public function index()
	{
		return;
	}

}
