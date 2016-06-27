<?php
include dirname(__FILE__) . '/clrz-check-in-wp.php';
global $clrz_core;
get_header(); ?>
<header id="main-header">
    <h2 class="mh-title"><?php echo __('Changer votre mot de passe'); ?></h2>
</header>
<div id="content">
    <div class="the_grid grid_login">
        <div>
            <div class="block_login login">
                <header>
                    <h3><?php echo __('Changer votre mot de passe'); ?></h3>
                </header>
                <div class="content">
                    <form action="<?php echo $clrz_core->_getUrl('changePassword', 'key='.$clrz_core->get_query_var('key').'&login='.$clrz_core->get_query_var('login')); ?>" autocomplete="off" class="cssn_form float_form register_form login_page_form" method="post" >
                        <fieldset>
                            <ul class="subfieldset">
                                <li class="box content-box box-password">
                                    <label for="newpasschange1">New</label>
                                    <input name="newpass" id="newpasschange1" type="password" value=""/>
                                </li>
                                <li class="box content-box box-password">
                                    <label for="newpasschange2">Confirm</label>
                                    <input name="newpass2" id="newpasschange2" type="password" value=""/>
                                </li>
                                <li class="nobox">
                                    <button class="le_btn submit_ql"></button>
                                </li>
                            </ul>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php get_footer();