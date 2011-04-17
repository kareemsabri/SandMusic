<?php
/**
* Twitter Component
*
* The Twitter API Component provides an easy way to access all of Twitters features including their Search API
*
* @version 0.1
* @author Dean Collins <dean@bigclick.com.au>
*
* @see http://www.dblog.com.au
*
* @project dblog
*/

App::import('Core', array('HttpSocket', 'Xml')); 
	
class TwitterComponent extends Object { 
	// Twitter Credentials
	var $username = 'XXXXXXXXXXXXXX';
	var $password = 'XXXXXXXXXXXXXX';
	
	// HTTP Socket
	var $http = NULL;
	var $http_method  = 'GET';
	
	/********************************************************************/
	/*																	*/
	/*					    PRIVATE FUNCTIONS							*/
	/*																	*/
	/********************************************************************/
	
	// Constructor
	function __construct() {
		// Init the http socket
		$this->http =& new HttpSocket();
	}
	
	/**
	* Process Request
	*
	* Process a url and return an object
	*
	* @param string $url
	* @param array $args
	* @param boolean $auth
	*/
	function __process_request($url, $args = NULL, $auth = true, $process = true) {
		if($auth) {
			// Build the request header (for authentication)
			$header = array();
			$header['auth'] = array();
			$header['auth']['method'] = 'Basic';
			$header['auth']['user'] = $this->username;
			$header['auth']['pass'] = $this->password;
		} else {
			$header = NULL;
		}
		
		// Send the request off
		if($this->http_method == 'GET') {
			$response = $this->http->get($url, $args, $header);
		} else {
			$this->http_method = 'GET';
			$response = $this->http->post($url, $args, $header);
		}
		
		if($process) {
			// Return the processed response
			return $this->__process_xml($response);
		} else {
			return $response;
		}
	}
	
	/**
	* Process XML Response
	*
	* Process the XML response from Twitter.com
	*
	* @param string $response
	*/
	function __process_xml($response) {
		$xml = new XML($response);
		$result = $xml->toArray();
		$xml->__killParent();
		$xml->__destruct();
		$xml = null;
		unset($xml);
		
		return $result;
	}
	
	
	/********************************************************************/
	/*																	*/
	/*					  TWITTER API FUNCTIONS							*/
	/*																	*/
	/********************************************************************/
	
	/********************************************************************/
	/**********************   TIMELINE METHODS   ************************/
	/********************************************************************/
	
	/**
	* statuses/public_timeline
	*
	* Returns the 20 most recent statuses from non-protected users who have set a custom user icon.
	* The public timeline is cached for 60 seconds so requesting it more often than that is a waste of resources.
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-statuses-public_timeline
	*
	*/
	function statuses_public_timeline() {
		$url = "http://twitter.com/statuses/public_timeline.xml";
		return $this->__process_request($url, NULL, false);
	}
	
	/**
	* statuses/friends_timeline
	*
	* Returns the 20 most recent statuses posted by the authenticating user and that user's friends.
	* This is the equivalent of /timeline/home on the Web.
	*
	* @param int since_id 	Optional.  Returns only statuses with an ID greater than (that is, more recent than) the specified ID. 
	* @param int max_id		Optional.  Returns only statuses with an ID less than (that is, older than) the specified ID.
	* @param int count		Optional.  Specifies the number of statuses to retrieve. May not be greater than 200. 
	* @param int page		Optional. Specifies the page of results to retrieve. Note: there are pagination limits.
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-statuses-friends_timeline
	*
	*/
	function statuses_friends_timeline($since_id = NULL, $max_id = NULL, $count = NULL, $page = NULL) {
		$args = array();
		if(!empty($since_id))
			$args['since_id'] = $since_id;
		if(!empty($max_id))
			$args['max_id'] = $max_id;
		if(!empty($count))
			$args['count'] = $count;
		if(!empty($page))
			$args['page'] = $page;
		
		$url = "http://twitter.com/statuses/friends_timeline.xml";
		return $this->__process_request($url, $args);
	}
	
	/**
	* statuses/user_timeline
	*
	* Returns the 20 most recent statuses posted from the authenticating user. It's also possible to request another user's timeline via the id 
	* parameter. This is the equivalent of the Web /<user> page for your own user, or the profile page for a third party.
	*
	* @param string id			Optional.  Specifies the ID or screen name of the user for whom to return the user_timeline. 
	* @param int user_id	Optional.  Specfies the ID of the user for whom to return the user_timeline. Helpful for disambiguating when a valid user ID is also a valid screen name.
	* @param string screen_name	Optional.  Specfies the screen name of the user for whom to return the user_timeline. Helpful for disambiguating when a valid screen name is also a user ID.
	* @param int since_id 	Optional.  Returns only statuses with an ID greater than (that is, more recent than) the specified ID. 
	* @param int max_id		Optional.  Returns only statuses with an ID less than (that is, older than) the specified ID.
	* @param int count		Optional.  Specifies the number of statuses to retrieve. May not be greater than 200. 
	* @param int page		Optional. Specifies the page of results to retrieve. Note: there are pagination limits.
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-statuses-user_timeline
	*
	*/
	function statuses_user_timeline($id = NULL, $user_id = NULL, $screen_name = NULL, $since_id = NULL, $max_id = NULL, $count = NULL, $page = NULL) {
		$args = array();
		if(!empty($id))
			$id = "/$id";;
		if(!empty($user_id))
			$args['user_id'] = $user_id;
		if(!empty($screen_name))
			$args['screen_name'] = $screen_name;
		if(!empty($since_id))
			$args['since_id'] = $since_id;
		if(!empty($max_id))
			$args['max_id'] = $max_id;
		if(!empty($count))
			$args['count'] = $count;
		if(!empty($page))
			$args['page'] = $page;
		
		$url = "http://twitter.com/statuses/user_timeline$id.xml";
		return $this->__process_request($url, $args);
	}
	
	/**
	* statuses/mentions
	*
	* Returns the 20 most recent mentions (status containing @username) for the authenticating user.
	*
	* @param int since_id 	Optional.  Returns only statuses with an ID greater than (that is, more recent than) the specified ID. 
	* @param int max_id		Optional.  Returns only statuses with an ID less than (that is, older than) the specified ID.
	* @param int count		Optional.  Specifies the number of statuses to retrieve. May not be greater than 200. 
	* @param int page		Optional. Specifies the page of results to retrieve. Note: there are pagination limits.
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-statuses-mentions
	*
	*/
	function statuses_mentions($since_id = NULL, $max_id = NULL, $count = NULL, $page = NULL) {
		$args = array();
		if(!empty($since_id))
			$args['since_id'] = $since_id;
		if(!empty($max_id))
			$args['max_id'] = $max_id;
		if(!empty($count))
			$args['count'] = $count;
		if(!empty($page))
			$args['page'] = $page;
		
		$url = "http://twitter.com/statuses/mentions.xml";
		return $this->__process_request($url, $args);
	}
	
	
	
	/********************************************************************/
	/************************   STATUS METHODS   ************************/
	/********************************************************************/
	
	/**
	* statuses/show
	*
	* Returns a single status, specified by the id parameter below.  The status's author will be returned inline.
	*
	* @param int id The numerical ID of the status to retrieve.  
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-statuses%C2%A0show
	*
	*/
	function statuses_show($id) {			
		$url = "http://twitter.com/statuses/show/$id.xml";
		return $this->__process_request($url);
	}
	
	/**
	* statuses/update
	*
	* Updates the authenticating user's status.  Requires the status parameter specified below.  Request must be a POST.  A status update
	* with text identical to the authenticating user's current status will be ignored to prevent duplicates.
	*
	* @param string status	Required.The text of your status update. URL encode as necessary. Statuses over 140 characters will be forceably truncated.
	* @param int in_reply_to_status_id	Optional. The ID of an existing status that the update is in reply to. 
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-statuses%C2%A0update
	*
	*/
	function statuses_update($status, $in_reply_to_status_id = NULL) {			
		$args['status'] = $status;
		if(!empty($in_reply_to_status_id))
			$args['in_reply_to_status_id'] = $in_reply_to_status_id;
		$url = "http://twitter.com/statuses/update.xml";
		$this->http_method = 'POST';
		return $this->__process_request($url, $args);
	}
	
	/**
	* statuses/destroy
	*
	* Destroys the status specified by the required ID parameter.  The authenticating user must be the author of the specified status.
	*
	* @param int id Required.  The ID of the status to destroy. 
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-statuses%C2%A0destroy
	*
	*/
	function statuses_destroy($id) {
		$this->http_method = 'POST';
		$url = "http://twitter.com/statuses/destroy/$id.xml";
		return $this->__process_request($url);
	}
	
	
	
	
	/********************************************************************/
	/*************************   USER METHODS   *************************/
	/********************************************************************/
	
	
	
	
	
	/**
	* users/show
	*
	* Returns extended information of a given user, specified by ID or screen name as per the required id parameter.  The author's most 
	* recent status will be returned inline.
	*
	* @param string id The ID or screen name of a user. 
	* @param boolean is_screenname Is the id above the users screenname 
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-users%C2%A0show
	*
	*/
	function users_show($id, $is_screenname = true) {
		$args = array();
		if($is_screenname) {
			$args['screen_name'] = $id;
		} else {
			$args['user_id'] = $id;
		}
		$url = "http://twitter.com/users/show.xml";
		return $this->__process_request($url, $args);
	}
	
	/**
	* statuses/friends
	*
	* Returns a user's friends, each with current status inline. They are ordered by the order in which they were added as friends.
	* Defaults to the authenticated user's friends. It's also possible to request another user's friends list via the id parameter.
	*
	* @param string id	The ID or screen name of a user. 
	* @param boolean is_screenname	Is the id above the users screenname 
	* @param int page	Optional. Specifies the page of friends to receive. 
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-statuses%C2%A0friends
	*
	*/
	function statuses_friends($id = NULL, $is_screenname = true, $page = NULL) {
		$args = array();
		if(!empty($id)) {
			if($is_screenname) {
				$args['screen_name'] = $id;
			} else {
				$args['user_id'] = $id;
			}
		}
		if(!empty($page))
			$args['page'] = $page;
		
		$url = "http://twitter.com/statuses/friends.xml";
		return $this->__process_request($url, $args);
	}
	
	/**
	* statuses/followers
	*
	* Returns the authenticating user's followers, each with current status inline.  They are ordered by the order in which they 
	* joined Twitter
	*
	* @param string id	The ID or screen name of a user. 
	* @param boolean is_screenname	Is the id above the users screenname 
	* @param int page	Optional. Specifies the page of friends to receive. 
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-statuses%C2%A0followers
	*
	*/
	function statuses_followers($id = NULL, $is_screenname = true, $page = NULL) {
		$args = array();
		if(!empty($id)) {
			if($is_screenname) {
				$args['screen_name'] = $id;
			} else {
				$args['user_id'] = $id;
			}
		}
		if(!empty($page))
			$args['page'] = $page;
		
		$url = "http://twitter.com/statuses/followers.xml";
		return $this->__process_request($url, $args);
	}
	
	
	
	/********************************************************************/
	/********************   DIRECT MESSAGE METHODS   ********************/
	/********************************************************************/
	
	
	
	/**
	* direct_messages
	*
	* Returns a list of the 20 most recent direct messages sent to the authenticating user.  The XML and JSON versions include detailed
	* information about the sending and recipient users.
	*
	* @param int since_id 	Optional.  Returns only direct messages with an ID greater than (that is, more recent than) the specified ID.
	* @param int max_id		Optional.  Returns only direct messages with an ID less than (that is, older than) the specified ID.
	* @param int count		Optional.  Specifies the number of statuses to retrieve. May not be greater than 200.
	* @param int page		Optional. Specifies the page of direct messages to retrieve.
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-direct_messages
	*
	*/
	function direct_messages($since_id = NULL, $max_id = NULL, $count = NULL, $page = NULL) {
		$args = array();
		if(!empty($since_id))
			$args['since_id'] = $since_id;
		if(!empty($max_id))
			$args['max_id'] = $max_id;
		if(!empty($count))
			$args['count'] = $count;
		if(!empty($page))
			$args['page'] = $page;
		
		$url = "http://twitter.com/direct_messages.xml";
		return $this->__process_request($url, $args);
	}
	
	/**
	* direct_messages/sent
	*
	* Returns a list of the 20 most recent direct messages sent to the authenticating user.  The XML and JSON versions include detailed
	* information about the sending and recipient users.
	*
	* @param int since_id 	Optional.  Returns only direct messages with an ID greater than (that is, more recent than) the specified ID.
	* @param int max_id		Optional.  Returns only direct messages with an ID less than (that is, older than) the specified ID. 
	* @param int page		Optional. Specifies the page of direct messages to retrieve. 
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-direct_messages%C2%A0sent
	*
	*/
	function direct_messages_sent($since_id = NULL, $max_id = NULL, $page = NULL) {
		$args = array();
		if(!empty($since_id))
			$args['since_id'] = $since_id;
		if(!empty($max_id))
			$args['max_id'] = $max_id;
		if(!empty($page))
			$args['page'] = $page;
		
		$url = "http://twitter.com/direct_messages/sent.xml";
		return $this->__process_request($url, $args);
	}
	
	/**
	* direct_messages/new
	*
	* Sends a new direct message to the specified user from the authenticating user. Requires both the user and text parameters. Request must
	* be a POST. Returns the sent message in the requested format when successful.
	*
	* @param string user Required.  The ID or screen name of the recipient user.
	* @param string text Required.  The text of your direct message.  Be sure to URL encode as necessary, and keep it under 140 characters.
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-direct_messages%C2%A0new
	*
	*/
	function direct_messages_new($user, $text) {			
		$args['user'] = $user;
		$args['text'] = $text;
		
		$url = "http://twitter.com/direct_messages/new.xml";
		$this->http_method = 'POST';
		return $this->__process_request($url, $args);
	}
	
	/**
	* direct_messages/destroy
	*
	* Destroys the direct message specified in the required ID parameter.  The authenticating user must be the recipient of the specified direct
	* message.
	*
	* @param int id Required.  The ID of the direct message to destroy.  
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-direct_messages%C2%A0destroy
	*
	*/
	function direct_messages_destroy($id) {
		$this->http_method = 'POST';
		$url = "http://twitter.com/direct_messages/destroy/$id.xml";
		return $this->__process_request($url);
	}
	
	
	
	/********************************************************************/
	/**********************   FRIENDSHIPS METHODS   *********************/
	/********************************************************************/
	
	
	
	
	/**
	* friendships/create
	*
	* Allows the authenticating users to follow the user specified in the ID parameter.  Returns the befriended user in the requested format when
	* successful.  Returns a string describing the failure condition when unsuccessful.
	*
	* @param string id	Required. The ID or screen name of the user to befriend.  
	* @param boolean is_screenname	Is the id above the users screenname 
	* @param boolean follow Optional. Enable notifications for the target user in addition to becoming friends. 
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-friendships%C2%A0create
	*
	*/
	function friendships_create($id = NULL, $is_screenname = true, $follow = true) {
		$this->http_method = 'POST';
		
		$args = array();
		if(!empty($id)) {
			if($is_screenname) {
				$args['screen_name'] = $id;
			} else {
				$args['user_id'] = $id;
			}
		}
		if(!empty($page))
			$args['page'] = $page;
			
		$url = "http://twitter.com/friendships/create.xml";
		return $this->__process_request($url, $args);
	}
	
	/**
	* friendships/destroy
	*
	* Allows the authenticating users to unfollow the user specified in the ID parameter.  Returns the unfollowed user in the requested
	* format when successful.  Returns a string describing the failure condition when unsuccessful.
	*
	* @param string id	Required. The ID or screen name of the user to unfollow.  
	* @param boolean is_screenname	Is the id above the users screenname 
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-friendships%C2%A0destroy
	*
	*/
	function friendships_destroy($id = NULL, $is_screenname = true) {
		$this->http_method = 'POST';
		
		$args = array();
		if(!empty($id)) {
			if($is_screenname) {
				$args['screen_name'] = $id;
			} else {
				$args['user_id'] = $id;
			}
		}
		
		$url = "http://twitter.com/friendships/destroy.xml";
		return $this->__process_request($url, $args);
	}
	
	/**
	* friendships/exists
	*
	* Tests for the existance of friendship between two users. Will return true if user_a follows user_b, otherwise will return false.
	*
	* @param string user_a	Required. The ID or screen_name of the subject user. 
	* @param string user_b	Required. The ID or screen_name of the user to test for following.
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-friendships-exists
	*
	*/
	function friendships_exists($user_a , $user_b) {
		$args = array();
		$args['user_a'] = $user_a;
		$args['user_b'] = $user_b;
		
		$url = "http://twitter.com/friendships/exists.xml";
		return $this->__process_request($url, $args);
	}
	
	
	
	/********************************************************************/
	/********************   SOCIAL GRAPHS METHODS   *********************/
	/********************************************************************/
	
	
	
	/**
	* friends/ids
	*
	* Returns an array of numeric IDs for every user the specified user is following.
	*
	* @param string id	Required. The ID or screen_name of the user to retrieve the friends ID list for.
	* @param boolean is_screenname	Is the id above the users screenname 
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-friends%C2%A0ids
	*
	*/
	function friends_ids($id = NULL, $is_screenname = true) {
		$args = array();
		if(!empty($id)) {
			if($is_screenname) {
				$args['screen_name'] = $id;
			} else {
				$args['user_id'] = $id;
			}
		}
		
		$url = "http://twitter.com/friends/ids.xml";
		return $this->__process_request($url, $args);
	}
	
	/**
	* followers/ids
	*
	* Returns an array of numeric IDs for every user following the specified user.
	*
	* @param string id	Required. The ID or screen_name of the user to retrieve the friends ID list for.
	* @param boolean is_screenname	Is the id above the users screenname 
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-followers%C2%A0ids
	*
	*/
	function followers_ids($id = NULL, $is_screenname = true) {
		$args = array();
		if(!empty($id)) {
			if($is_screenname) {
				$args['screen_name'] = $id;
			} else {
				$args['user_id'] = $id;
			}
		}
		
		$url = "http://twitter.com/followers/ids.xml";
		return $this->__process_request($url, $args);
	}
	
	
	/********************************************************************/
	/********************   SOCIAL GRAPHS METHODS   *********************/
	/********************************************************************/
	
	
	
	/**
	* account/verify_credentials
	*
	* Returns an HTTP 200 OK response code and a representation of the requesting user if authentication was successful; returns a 401
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-account%C2%A0verify_credentials
	*
	*/
	function account_verify_credentials() {
		$url = "http://twitter.com/account/verify_credentials.xml";
		return $this->__process_request($url);
	}
	
	/**
	* account/rate_limit_status
	*
	* Returns the remaining number of API requests available to the requesting user before the API limit is reached for the current hour. 
	* Calls to rate_limit_status do not count against the rate limit.  If authentication credentials are provided, the rate limit status for the 
	* authenticating user is returned.  Otherwise, the rate limit status for the requester's IP address is returned. Learn more about the REST 
	* API rate limiting.
	*
	* @param boolean $check_ip Optional. Check the requesting IP's rate limit
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-account%C2%A0rate_limit_status
	*
	*/
	function account_rate_limit_status($check_ip = false) {
		$url = "http://twitter.com/account/rate_limit_status.xml";
		return $this->__process_request($url, NULL, !$check_ip);
	}
	
	/**
	* account/end_session
	*
	* Ends the session of the authenticating user, returning a null cookie.  Use this method to sign users out of client-facing applications
	* like widgets.
	*
	* @param boolean $check_ip Optional. Check the requesting IP's rate limit
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-account%C2%A0rate_limit_status
	*
	*/
	function account_end_session() {
		$this->http_method = 'POST';
		$url = "http://twitter.com/account/rate_limit_status.xml";
		return $this->__process_request($url);
	}
	
	/**
	* account/update_delivery_device
	*
	* Sets which device Twitter delivers updates to for the authenticating user.  Sending none as the device parameter will disable IM or SMS 
	* updates.
	*
	* @param string $device Required.  Must be one of: sms, im, none.
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-account%C2%A0update_delivery_device
	*
	*/
	function account_update_delivery_device($device) {
		$valid_devices = array('sms', 'im', 'none');
		if(in_array($device, $valid_devices)) {
			$this->http_method = 'POST';
			$url = "http://twitter.com/account/update_delivery_device.xml";
			return $this->__process_request($url);
		} else {
			return false;
		}
	}
	
	/**
	* account/update_profile_colors
	*
	* Sets one or more hex values that control the color scheme of the authenticating user's profile page on twitter.com.
	*
	* @param string $profile_background_color Optional.
	* @param string $profile_text_color Optional.
	* @param string $profile_link_color Optional.
	* @param string $profile_sidebar_fill_color Optional.
	* @param string $profile_sidebar_border_color Optional.
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-account%C2%A0update_profile_colors
	*
	*/
	function account_update_profile_colors($profile_background_color = NULL, $profile_text_color = NULL, $profile_link_color = NULL, $profile_sidebar_fill_color = NULL, $profile_sidebar_border_color = NULL) {
		$args = array();
		if(preg_match('/^([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?$/i', $profile_background_color))
			$args['profile_background_color'] = $profile_background_color;
		if(preg_match('/^([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?$/i', $profile_text_color))
			$args['profile_text_color'] = $profile_text_color;
		if(preg_match('/^([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?$/i', $profile_link_color))
			$args['profile_link_color'] = $profile_link_color;
		if(preg_match('/^([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?$/i', $profile_sidebar_fill_color))
			$args['profile_sidebar_fill_color'] = $profile_sidebar_fill_color;
		if(preg_match('/^([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?$/i', $profile_sidebar_border_color))
			$args['profile_sidebar_border_color'] = $profile_sidebar_border_color;
		
		
		if(sizeof($args) > 0) {
			$this->http_method = 'POST';
			$url = "http://twitter.com/account/update_profile_colors.xml";
			return $this->__process_request($url, $args);
		} else {
			return false;
		}
	}
	
	/**
	* account/update_profile_image
	*
	* Updates the authenticating user's profile image. Note that this method expects a valid filepath.
	*
	* @param string $image Required.  Must be a valid filepath. Must be a valid GIF, JPG, or PNG image of less than 500 kilobytes in size.
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-account%C2%A0update_profile_image
	*
	*/
	function account_update_profile_image($image) {
		$args = array();
		if(file_exists($image)) {
			$args['image'] = "@$image";
		}
		
		if(isset($args['image'])) {
			$url = "http://twitter.com/account/update_profile_image.xml";
			$curl_handle = curl_init();
			curl_setopt($curl_handle, CURLOPT_URL, "$url");
			curl_setopt($curl_handle, CURLOPT_POST, 1);
			curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array('Expect:'));
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
			curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $args);
			curl_setopt($curl_handle, CURLOPT_USERPWD, $this->username . ":" . $this->password);
			$buffer = curl_exec($curl_handle);
			
			curl_close($curl_handle);
			return $this->__process_xml($buffer);
		} else {
			return false;
		}
	}
	
	/**
	* account/update_profile_background_image
	*
	* Updates the authenticating user's profile background image. Note that this method expects a valid filepath.
	*
	* @param string $image Required.  Must be a valid filepath. Must be a valid GIF, JPG, or PNG image of less than 800 kilobytes in size.
	* @param boolean tile Optional. If set to true the background image will be displayed tiled. The image will not be tiled otherwise.
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-account%C2%A0update_profile_background_image
	*
	*/
	function account_update_profile_background_image($image, $tile) {
		$args = array();
		if(file_exists($image)) {
			$args['image'] = "@$image";
		}
		if($tile)
			$args['tile'] = 'true';
		
		if(isset($args['image'])) {
			$url = "http://twitter.com/account/update_profile_background_image.xml";
			$curl_handle = curl_init();
			curl_setopt($curl_handle, CURLOPT_URL, "$url");
			curl_setopt($curl_handle, CURLOPT_POST, 1);
			curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array('Expect:'));
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
			curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $args);
			curl_setopt($curl_handle, CURLOPT_USERPWD, $this->username . ":" . $this->password);
			$buffer = curl_exec($curl_handle);
			
			curl_close($curl_handle);
			return $this->__process_xml($buffer);
		} else {
			return false;
		}
	}
	
	/**
	* account/update_profile
	*
	* Sets which device Twitter delivers updates to for the authenticating user.  Sending none as the device parameter will disable IM or SMS 
	* updates.
	*
	* @param string $name Optional. Maximum of 20 characters.
	* @param string $email Optional. Maximum of 40 characters. Must be a valid email address.
	* @param string $url Optional. Maximum of 100 characters. Will be prepended with "http://" if not present.
	* @param string $location Optional. Maximum of 30 characters. The contents are not normalized or geocoded in any way.
	* @param string $description Optional. Maximum of 160 characters.
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-account%C2%A0update_profile
	*
	*/
	function account_update_profile($name = NULL, $email = NULL, $url = NULL, $location = NULL, $description = NULL) {
		$args = array();
		if(!empty($name))
			$args['name'] = $name;
		if(!empty($email))
			$args['email'] = $email;
		if(!empty($url))
			$args['url'] = $url;
		if(!empty($description))
			$args['description'] = $description;
			
		if(sizeof($args) > 0) {
			$this->http_method = 'POST';
			$url = "http://twitter.com/account/update_profile.xml";
			return $this->__process_request($url, $args);
		} else {
			return false;
		}
	}
	
	
	
	/********************************************************************/
	/**********************   FAVORITE METHODS   ************************/
	/********************************************************************/
	
	
	
	/**
	* favorites
	*
	* Returns the 20 most recent favorite statuses for the authenticating user or user specified by the ID parameter in the requested format.
	*
	* @param string id 	Optional.  The ID or screen name of the user for whom to request a list of favorite statuses. 
	* @param int page	Optional. Specifies the page of favorites to retrieve. 
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-favorites
	*
	*/
	function favorites($id = NULL, $page = NULL) {
		$args = array();
		if(!empty($id))
			$id = "/$id";
		if(!empty($page))
			$args['page'] = $page;
		
		$url = "http://twitter.com/favorites$id.xml";
		return $this->__process_request($url, $args);
	}
	
	/**
	* favorites/create
	*
	* Favorites the status specified in the ID parameter as the authenticating user. Returns the favorite status when successful.
	*
	* @param int id Required.  The ID of the status to favorite. 
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-favorites%C2%A0create
	*
	*/
	function favorites_create($id = NULL) {
		$this->http_method = "POST";
		
		$url = "http://twitter.com/favorites/create/$id.xml";
		return $this->__process_request($url);
	}
	
	/**
	* favorites/destroy
	*
	* Un-favorites the status specified in the ID parameter as the authenticating user. Returns the un-favorited status in the requested
	* format when successful.
	*
	* @param int id Required.  The ID of the status to un-favorite. 
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-favorites%C2%A0destroy
	*
	*/
	function favorites_destroy($id = NULL) {
		$this->http_method = "POST";
		
		$url = "http://twitter.com/favorites/destroy/$id.xml";
		return $this->__process_request($url);
	}
	
	
	
	/********************************************************************/
	/********************   NOTIFICATION METHODS   **********************/
	/********************************************************************/
	
	
	
	/**
	* notifications/follow
	*
	* Enables device notifications for updates from the specified user.  Returns the specified user when successful.
	*
	* @param string id	The ID or screen name of a user. 
	* @param boolean is_screenname	Is the id above the users screenname 
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-notifications%C2%A0follow
	*
	*/
	function notifications_follow($id = NULL, $is_screenname = true) {
		$args = array();
		if(!empty($id)) {
			if($is_screenname) {
				$args['screen_name'] = $id;
			} else {
				$args['user_id'] = $id;
			}
		}
		
		$this->http_method = "POST";
		$url = "http://twitter.com/notifications/follow.xml";
		return $this->__process_request($url, $args);
	}
	
	/**
	* notifications/leave
	*
	* Disables notifications for updates from the specified user to the authenticating user.  Returns the specified user when 
	* successful.
	*
	* @param string id	The ID or screen name of a user. 
	* @param boolean is_screenname	Is the id above the users screenname 
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-notifications%C2%A0leave
	*
	*/
	function notifications_leave($id = NULL, $is_screenname = true) {
		$args = array();
		if(!empty($id)) {
			if($is_screenname) {
				$args['screen_name'] = $id;
			} else {
				$args['user_id'] = $id;
			}
		}
		
		$this->http_method = "POST";
		$url = "http://twitter.com/notifications/leave.xml";
		return $this->__process_request($url, $args);
	}
	
	
	
	/********************************************************************/
	/********************   BLOCK METHODS   **********************/
	/********************************************************************/
	
	
	
	/**
	* blocks/create
	*
	* Blocks the user specified in the ID parameter as the authenticating user.  Returns the blocked user in the requested format 
	* when successful.  You can find out more about blocking in the Twitter Support Knowledge Base.
	*
	* @param string id Required. The ID or screen name of a user. 
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-blocks%C2%A0create
	*
	*/
	function blocks_create($id) {
		$this->http_method = "POST";
		$url = "http://twitter.com/blocks/create/$id.xml";
		return $this->__process_request($url);
	}
	
	/**
	* blocks/destroy
	*
	* Un-blocks the user specified in the ID parameter for the authenticating user.  Returns the un-blocked user in the requested 
	* format when successful. 
	*
	* @param string id Required. The ID or screen name of a user. 
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-blocks%C2%A0destroy
	*
	*/
	function blocks_destroy($id) {
		$this->http_method = "POST";
		$url = "http://twitter.com/blocks/destroy/$id.xml";
		return $this->__process_request($url);
	}
	
	
	
	/********************************************************************/
	/*************************   HELP METHODS   *************************/
	/********************************************************************/
	
	
	
	/**
	* help/test
	*
	* Returns the string "ok" in the requested format with a 200 OK HTTP status code.
	*
	* @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-help%C2%A0test
	*
	*/
	function help_test() {
		$url = "http://twitter.com/help/test";
		return $this->__process_request($url);
	}
	
	
	
	/********************************************************************/
	/************************   SEARCH METHODS   ************************/
	/********************************************************************/
	
	
	
	/**
	* search
	*
	* Returns tweets that match a specified query.
	*
	* @param string $q 	Optional.  
	* @param string $lang 	Optional: Restricts tweets to the given language, given by an ISO 639-1 code. 
	* @param int $rpp 	Optional. The number of tweets to return per page, up to a max of 100. 
	* @param int $page 	Optional. The page number (starting at 1) to return, up to a max of roughly 1500 results (based on rpp * page. Note: there are pagination limits. 
	* @param int $since_id 	Optional. Returns tweets with status ids greater than the given id. 
	* @param string $geocode Optional. Returns tweets by users located within a given radius of the given latitude/longitude, where the user's location is taken from their Twitter profile. The parameter value is specified by "latitide,longitude,radius", where radius units must be specified as either "mi" (miles) or "km" (kilometers). Note that you cannot use the near operator via the API to geocode arbitrary locations; however you can use this geocode parameter to search near geocodes directly.  
	*
	* @see http://apiwiki.twitter.com/Twitter-Search-API-Method%3A-search
	*
	*/
	function search($q = NULL, $lang = NULL, $rpp = NULL, $page = NULL, $since_id = NULL, $geocode = NULL) {
		$args = array();
		if(!empty($q))
			$args['q'] = $q;
		if(!empty($lang))
			$args['lang'] = $lang;
		if(!empty($rpp))
			$args['rpp'] = $rpp;
		if(!empty($page))
			$args['page'] = $page;
		if(!empty($since_id))
			$args['since_id'] = $since_id;
		if(!empty($geocode))
			$args['geocode'] = $geocode;
		
		
		$url = "http://search.twitter.com/search.json";
		return json_decode($this->__process_request($url, $args, false, false));
	}
	
	/**
	* trends
	*
	* Returns the top ten topics that are currently trending on Twitter.  The response includes the time of the request, the name of each
	* trend, and the url to the Twitter Search results page for that topic.
	*
	* @see http://apiwiki.twitter.com/Twitter-Search-API-Method%3A-trends
	*
	*/
	function trends() {
		$url = "http://search.twitter.com/trends.json";
		return json_decode($this->__process_request($url, NULL, false, false));
	}
	
	/**
	* trends/current
	*
	* Returns the current top 10 trending topics on Twitter.  The response includes the time of the request, the name of each trending topic,
	* and query used on Twitter Search results page for that topic.
	*
	* @param string $exclude Optional. Setting this equal to 'hashtags' will remove all hashtags from the trends list.
	*
	* @see http://apiwiki.twitter.com/Twitter-Search-API-Method%3A-trends-current
	*
	*/
	function trends_current($exclude = NULL) {
		$args = array();
		if(!empty($exclude))
			$args['exclude'] = $exclude;
			
		$url = "http://search.twitter.com/trends/current.json";
		return json_decode($this->__process_request($url, $args, false, false));
	}
	
	/**
	* trends/daily
	*
	* Returns the top 20 trending topics for each hour in a given day.
	*
	* @param string $date Optional. Optional. Permits specifying a start date for the report. The date should be formatted YYYY-MM-DD.
	* @param string $exclude Optional. Setting this equal to 'hashtags' will remove all hashtags from the trends list.
	*
	* @see http://apiwiki.twitter.com/Twitter-Search-API-Method%3A-trends-daily
	*
	*/
	function trends_daily($date = NULL, $exclude = NULL) {
		$args = array();
		if(!empty($exclude))
			$args['exclude'] = $exclude;
		if(!empty($date))
			$args['date'] = $date;
			
		$url = "http://search.twitter.com/trends/daily.json";
		return json_decode($this->__process_request($url, $args, false, false));
	}
	
	/**
	* trends/weekly
	*
	* Returns the top 30 trending topics for each day in a given week.
	*
	* @param string $date Optional. Optional. Permits specifying a start date for the report. The date should be formatted YYYY-MM-DD.
	* @param string $exclude Optional. Setting this equal to 'hashtags' will remove all hashtags from the trends list.
	*
	* @see http://apiwiki.twitter.com/Twitter-Search-API-Method%3A-trends-weekly
	*
	*/
	function trends_weekly($date = NULL, $exclude = NULL) {
		$args = array();
		if(!empty($exclude))
			$args['exclude'] = $exclude;
		if(!empty($date))
			$args['date'] = $date;
			
		$url = "http://search.twitter.com/trends/weekly.json";
		return json_decode($this->__process_request($url, $args, false, false));
	}
	
}
?>