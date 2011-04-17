<?php

/**************************************************************
Name: App Controller
File: /app/app_controller.php
Created: September 25, 2010
Author: Kareem Sabri
**************************************************************/

class AppController extends Controller {
	var $components = array('Cookie', 'RequestHandler', 'Session');
	var $helpers = array('Js' => array('Prototype'));
	function beforeFilter() {
		$this->Cookie->name = 'SandMusic';
		$this->Cookie->time =  3600*24*365;  // 1 year cookie
		$this->Cookie->path = '/'; 
		$this->Cookie->domain = 'sandmusic.ca';   
		$this->Cookie->secure = false;
		$this->Cookie->key = 'XXXXXXXXXXXXXXXXXXX';
	  
		//twitter credentials
		define("CONSUMER_KEY", "XXXXXXXXXXXXXXXXXXX");
		define("CONSUMER_SECRET", "XXXXXXXXXXXXXXXXXXX");
		define("OAUTH_TOKEN", "XXXXXXXXXXXXXXXXXXX-XXXXXXXXXXXXXXXXXXX");
		define("OAUTH_SECRET", "XXXXXXXXXXXXXXXXXXX");
		define('FACEBOOK_APP_ID', 'XXXXXXXXXXXXXXXXXXX');
		define('FACEBOOK_SECRET', 'XXXXXXXXXXXXXXXXXXX');
		
		if ($this->params['controller']=='pages') {
			$this->set('pages', true);
		}		
	}

	function _doLogin($user_id, $third_party_site=null) {
		$this->loadModel('User');
		$user = $this->User->findById($user_id);
		$this->Session->write('user_id', $user['User']['id']);
		$this->Session->write('name', $user['User']['first_name']);
		$this->Session->write('fb_share', $user['User']['facebook_share']);
		
		if ($third_party_site=='fb') {
			$this->Session->write('fb', true);
		}
		
		$this->User->id = $user['User']['id'];
		$data = array(
			'User' => array(
				'logged_in' => date('Y-m-d H:i:s')
			)
		);
		$this->User->save($data);
	}
	
	function _signedIn() {
		App::import('Vendor', 'facebook/facebook');
		$facebook = new Facebook(
			array(
					'appId'  => FACEBOOK_APP_ID,
					'secret' => FACEBOOK_SECRET,
					'cookie' => true,
			)
		);
		
		if(is_null($facebook->getUser())) {
			$this->Session->destroy();
			return false;
		} else {
			return true;
		}
	}
	
	function _init() {
		date_default_timezone_set('America/Toronto');
		$info['title_prefix'] = 'SandMusic ';
		$info['web_prefix'] = '/';
		$info['signed_in'] = false;
		if ($this->_signedIn()) {
			$info['signed_in'] = true;
			$this->set('name', $this->Session->read('name'));
			$info['user_id'] = $this->Session->read('user_id');
		} else {
			$info['signed_in'] = false;
		}
		
		$this->set('info', $info);
		return $info;
	}
	
    function _ago($datefrom,$dateto=-1,$timestamp=false) {
        // Defaults and assume if 0 is passed in that
        // its an error rather than the epoch
    
        if($datefrom==0) { return "A long time ago"; }
        if($dateto==-1) { $dateto = time(); }
        
        // Make the entered date into Unix timestamp from MySQL datetime field
		
		if (!$timestamp) {
        	$datefrom = strtotime($datefrom);
    	}
        // Calculate the difference in seconds betweeen
        // the two timestamps

        $difference = $dateto - $datefrom;

        // Based on the interval, determine the
        // number of units between the two dates
        // From this point on, you would be hard
        // pushed telling the difference between
        // this function and DateDiff. If the $datediff
        // returned is 1, be sure to return the singular
        // of the unit, e.g. 'day' rather 'days'
    
        switch(true)
        {
            // If difference is less than 60 seconds,
            // seconds is a good interval of choice
            case(strtotime('-1 min', $dateto) < $datefrom):
                $datediff = $difference;
                $res = ($datediff==1) ? $datediff.' sec ago' : $datediff.' secs ago';
                break;
            // If difference is between 60 seconds and
            // 60 minutes, minutes is a good interval
            case(strtotime('-1 hour', $dateto) < $datefrom):
                $datediff = floor($difference / 60);
                $res = ($datediff==1) ? $datediff.' min ago' : $datediff.' mins ago';
                break;
            // If difference is between 1 hour and 24 hours
            // hours is a good interval
            case(strtotime('-1 day', $dateto) < $datefrom):
                $datediff = floor($difference / 60 / 60);
                $res = ($datediff==1) ? $datediff.' hr ago' : $datediff.' hrs ago';
                break;
            // If difference is between 1 day and 7 days
            // days is a good interval                
            case(strtotime('-1 week', $dateto) < $datefrom):
                $day_difference = 1;
                while (strtotime('-'.$day_difference.' day', $dateto) >= $datefrom)
                {
                    $day_difference++;
                }
                
                $datediff = $day_difference;
                $res = ($datediff==1) ? 'yesterday' : $datediff.' days ago';
                break;
            // If difference is between 1 week and 30 days
            // weeks is a good interval            
            case(strtotime('-1 month', $dateto) < $datefrom):
                $week_difference = 1;
                while (strtotime('-'.$week_difference.' week', $dateto) >= $datefrom)
                {
                    $week_difference++;
                }
                
                $datediff = $week_difference;
                $res = ($datediff==1) ? 'last week' : $datediff.' weeks ago';
                break;            
            // If difference is between 30 days and 365 days
            // months is a good interval, again, the same thing
            // applies, if the 29th February happens to exist
            // between your 2 dates, the function will return
            // the 'incorrect' value for a day
            case(strtotime('-1 year', $dateto) < $datefrom):
                $months_difference = 1;
                while (strtotime('-'.$months_difference.' month', $dateto) >= $datefrom)
                {
                    $months_difference++;
                }
                
                $datediff = $months_difference;
                $res = ($datediff==1) ? $datediff.' month ago' : $datediff.' months ago';

                break;
            // If difference is greater than or equal to 365
            // days, return year. This will be incorrect if
            // for example, you call the function on the 28th April
            // 2008 passing in 29th April 2007. It will return
            // 1 year ago when in actual fact (yawn!) not quite
            // a year has gone by
            case(strtotime('-1 year', $dateto) >= $datefrom):
                $year_difference = 1;
                while (strtotime('-'.$year_difference.' year', $dateto) >= $datefrom)
                {
                    $year_difference++;
                }
                
                $datediff = $year_difference;
                $res = ($datediff==1) ? $datediff.' year ago' : $datediff.' years ago';
                break;
                
        }
        
        return $res;
	}
	
	function _get_facebook_cookie($app_id, $application_secret) {
		$args = array();
		parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
		
		ksort($args);
		$payload = '';
		foreach ($args as $key => $value) {
			if ($key != 'sig') {
			  $payload .= $key . '=' . $value;
			}
		}
		if (md5($payload . $application_secret) != $args['sig']) {
			return null;
		}
			return $args;		
	}

}

?>