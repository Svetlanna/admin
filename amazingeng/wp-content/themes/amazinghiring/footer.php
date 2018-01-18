    
    <footer>
        <div class="container">
            <div class="footer-table-div">
                <?php 
                $url = $_SERVER["REQUEST_URI"];

                $isItEn = strpos($url, '/');

                if ($isItEn!==false)
                { ?>
                    <div class="copyright">
                        © 2017 AmazingHiring <br>
                        541 Jefferson Avenue, Suite 100, Redwood City, CA 94063 <br>
                        <a href="mailto:sales@amazinghiring.com">sales@amazinghiring.com</a>
                    </div>
                <?php }else{?>
                    <div class="copyright">
                        © 2017 AmazingHiring <br>
                        117105, Россия, Москва, Варшавское ш., д. 1, стр. 6, БЦ W Plaza 2, офис А103 <br>
                        +7 499 394 6347 <br>
                        <a href="mailto:sales@amazinghiring.com">sales@amazinghiring.com</a>
                    </div>
                <?php } ?>

                <!-- <div class="buttons">
                    <ul>
                        <li><a href="#">Продукт</a></li>
                        <li><a href="#">База знаний</a></li>
                        <li><a class="active" href="#">Блог</a></li>
                    </ul>
                </div>
                 -->
                <?php wp_nav_menu('menu=Down&menu_class=buttons'); ?>
                <div class="contacts">
                    <!-- <span><i class="fa fa-mobile"></i> +7 495 743 9860</span>
                    <span><i class="fa fa-envelope-o"></i> <a href="mailto:sales@amazinghiring.com">sales@amazinghiring.com</a></span> -->
                    <?php wp_nav_menu('menu=Lang&menu_class=botlang'); ?>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </footer>
    <div class="bg-layout"></div>
    <script src="<?php bloginfo('template_directory');?>/js/jquery1-11.min.js"></script>
    <script src="<?php bloginfo('template_directory');?>/js/jquery.cookie.js"></script>
    <script src="<?php bloginfo('template_directory');?>/js/parallax.min.js"></script>
    <script src="<?php bloginfo('template_directory');?>/js/material.min.js"></script>
    <script src="<?php bloginfo('template_directory');?>/js/jquery.viewportchecker.js"></script>
    <script src="<?php bloginfo('template_directory');?>/js/jquery.equalheight.js"></script>
    <script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
    <script src="//yastatic.net/share2/share.js"></script>

    <script src="//cdnjs.cloudflare.com/ajax/libs/js-cookie/2.1.4/js.cookie.min.js"></script>
    <script src="<?php bloginfo('template_directory');?>/js/utm.js"></script>
    
    <script src="<?php bloginfo('template_directory');?>/js/scripts.js"></script>
    <?php wp_footer(); ?> 
</body>
</html>
