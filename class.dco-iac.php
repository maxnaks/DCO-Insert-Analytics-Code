<?php
defined('ABSPATH') or die;

class DCO_IAC extends DCO_IAC_Base {

    public function __construct() {
        add_action('init', array($this, 'init_hooks'));
    }

    protected function check_show($option_name) {
        $user_logged = is_user_logged_in();
        $show = apply_filters('dco_iac_insert_' . $option_name . '_show', $this->options[$option_name . '_show']);

        switch ($show) {
            case '1':
                return !$user_logged;
            case '2':
                return $user_logged;
            default:
                return true;
        }
    }

    public function init_hooks() {
        parent::init_hooks();

        if (!empty($this->options['before_head']) && $this->check_show('before_head')) {
            add_action('wp_head', array($this, 'insert_before_head'), 99);
        }

        if (!empty($this->options['after_body']) && $this->check_show('after_body')) {
            add_filter('template_include', array($this, 'insert_after_body'), 99);
        }

        if (!empty($this->options['before_body']) && $this->check_show('before_body')) {
            add_action('wp_footer', array($this, 'insert_before_body'), 99);
        }
    }

    /**
     * Insert code before </head>
     */
    public function insert_before_head() {
        echo apply_filters('dco_iac_insert_before_head', $this->options['before_head'] . "\n");
    }

    /**
     * Insert code after <body>
     */
    public function insert_after_body($template) {
        ob_start(array($this, 'insert_after_body_code'));

        return $template;
    }

    /**
     * Insert code before </body>
     */
    public function insert_before_body() {
        echo apply_filters('dco_iac_insert_before_body', $this->options['before_body'] . "\n");
    }

    /**
     * Helper function for "insert_after_body"
     */
    public function insert_after_body_code($buffer) {
        $buffer = preg_replace('@<body[^>]*>@', '$0' . apply_filters('dco_iac_insert_after_body', "\n" . $this->options['after_body']), $buffer);

        return $buffer;
    }

}

$dco_iac = new DCO_IAC();
