<?php

Class ClrzPostAbstract {

    private $edited = false;

    public function __construct($post='') {
        if ($post)
            $this->fill($post);
        return $this;
    }

    function load($ID) {

        global $wpdb;
        $post = $wpdb->get_row('SELECT * FROM ' . $wpdb->posts . ' WHERE ID = "' . mysql_real_escape_string($ID) . '"');
        return $this->fill($post);
    }

    function fill($data) {

        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $this->data->{$k} = $v;
            }
        }
        else
            $this->data = $data;

        return $this;
    }

    public function getData($key='') {


        if (!$key)
            return $this->data;

        if (!$this->data)
            return false;

        if ($this->data->{$key}) {
            return $this->data->{$key};
        } else {

            return get_post_meta($this->getID(), strtolower($key), true);
        }
    }

    public function setData($key, $val) {

        if (!$this->data)
            return false;


        $this->data->{$key} = $val;

        $this->edited = true;
        return $this;
    }

    public function getColums() {

        global $wpdb;
        $cols = array();
        foreach ($wpdb->get_results('SHOW COLUMNS FROM ' . $wpdb->posts) as $col) {

            $cols[$col->Field] = true;
        }
        return $cols;
    }

    public function setPostAuthor($var) {

        return $this->setAuthor($var);
    }

    public function setAuthor($var) {

        if (is_numeric($var))
            return $this->setData('post_author', $var);

        global $wpdb;
        $res = $wpdb->get_row('SELECT ID FROM ' . $wpdb->users . ' WHERE user_login ="' . mysql_real_escape_string($var) . '" OR user_email="' . mysql_real_escape_string($var) . '"');
        return $this->setData('post_author', $res->ID);
    }

    public function save() {



        if (!$this->edited)
            return false;

        $this->setData('ID', wp_insert_post((array) $this->data));


        foreach (array_diff_key((array) $this->data, (array) $this->getColums()) AS $k => $v) {

            update_post_meta($this->getID(), strtolower($k), $v);
        }
        $this->edited = false;
        return $this;
    }

    public function getID() {
        if ($this->data->ID)
            return $this->getData('ID');
        else
            return false;
    }

    public function __call($name, $params) {

        $action = substr($name, 0, 3);
        $var = $this->uncamelize(substr($name, 3));

        switch ($action) {
            case 'get':
                return $this->getData($var);
                break;
            case 'set':
                return $this->setData($var, $params[0]);
                break;
            default:
                return false;
                break;
        }
    }

    public function uncamelize($camel, $splitter="_") {
        $camel = preg_replace('/(?!^)[[:upper:]][[:lower:]]/', '$0', preg_replace('/(?!^)[[:upper:]]+/', $splitter . '$0', $camel));
        return strtolower($camel);
    }

}
