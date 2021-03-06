<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RTMediaComment
 *
 * @author udit
 */
class RTMediaComment {

	var $rtmedia_comment_model;

	public function __construct() {
		$this->rtmedia_comment_model = new RTMediaCommentModel();
	}

	static function comment_nonce_generator($echo = true) {
		if($echo) {
			wp_nonce_field('rtmedia_comment_nonce','rtmedia_comment_nonce');
		} else {
			$token = array(
				'action' => 'rtmedia_comment_nonce',
				'nonce' => wp_create_nonce('rtmedia_comment_nonce')
			);

			return json_encode($token);
		}
	}

	/**
	 * returns user_id of the current logged in user in wordpress
	 *
	 * @global type $current_user
	 * @return type
	 */
	function get_current_id() {

		global $current_user;
		get_currentuserinfo();
		return $current_user->ID;
	}

	/**
	 * returns user_id of the current logged in user in wordpress
	 *
	 * @global type $current_user
	 * @return type
	 */
	function get_current_author() {

		global $current_user;
		get_currentuserinfo();
		return $current_user->user_login;
	}

	function add($attr) {

	       do_action('rtmedia_before_add_comment', $attr);
               $defaults = array(
                        'user_id'           => $this->get_current_id(),
                        'comment_author'    => $this->get_current_author(),
                        'comment_date'      =>  current_time('mysql')
                );
                $params = wp_parse_args( $attr, $defaults );
		
		$id = $this->rtmedia_comment_model->insert($params);
		global $rtmedia_points_media_id;
		$rtmedia_points_media_id = rtmedia_id($params['comment_post_ID']);
		do_action('rtmedia_after_add_comment', $params);

		return $id;
	}

	function remove($id) {

		do_action('rtmedia_before_remove_comment', $id);
                
                $comment = "";
                if(!empty($id)) {
                    $comment = get_comment( $id );
                }
                
                if(!is_rt_admin() && isset( $comment->user_id ) && $comment->user_id != get_current_user_id() )
                    return;
                
                $comment_deleted = $this->rtmedia_comment_model->delete($id);
                
                do_action('rtmedia_after_remove_comment', $id);
                
                return $comment_deleted;
	}
}
