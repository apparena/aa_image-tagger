<?php
class Frd_Check_Session
{
    /*
     * session.save_handler = files
     * session.save_path = "N;/path"
     * session.use_cookies = 1
     * session.name = YGXSID
     * session.auto_start = 0
     * session.cookie_lifetime = 0
     * session.cookie_path = /
     * session.cookie_domain =
     * session.serialize_handler = php
     * session.gc_probability = 1
     * session.gc_divisor     = 1000
     * session.gc_maxlifetime = 72000
     * session.bug_compat_42 = 0
     * session.bug_compat_warn = 1
     * session.referer_check =
     * session.entropy_length = 0
     * session.entropy_file =
     * session.cache_limiter =
     * session.cache_expire = 600
     * session.use_trans_sid = 0
     * session.hash_function = 0
     * session.hash_bits_per_character = 5
     */

    function __construct()
    {
        $this->savepath=session_save_path(); 
        $this->prev="sess_";
        $this->cookiename=ini_get("session.name");
    }

    function run()
    {
        session_start();
        $_SESSION['Frd_Check_Session']='CHECK';

        $session_file=$this->savepath.'/'.$this->prev.session_id();

        if(file_exists($session_file) === false)
            throw new Exception("session created,$session_file not exists!");

        if(file_get_contents($session_file) === false)
            throw new Exception("session created,$session_file read return false");
        //echo $this->cookiename;
        //print_r($_COOKIE);
        return true;
    }
}
