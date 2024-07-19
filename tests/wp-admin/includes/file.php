<?php

class WP_Filesystem_Base
{
    public function put_contents(string $filename, string $content)
    {

    }
}

function WP_Filesystem() {
    global $wp_filesystem;
    $wp_filesystem = new WP_Filesystem_Base();
}
