<?php
/**
 * 登录用户UID
 * @return number
 */
function ss_uid(){
	return session('userId');
}
/**
 * 用户所属学院id
 * @return number
 */
function ss_clgid(){
	return session('collegeId');
}
/**
 * 登录学生所在班级id
 * @return number
 */
function ss_clsid(){
	return session('classId');
}