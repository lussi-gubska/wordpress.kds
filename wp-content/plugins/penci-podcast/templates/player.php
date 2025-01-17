<div class="pencipdc_podcast pencipdc_dock_player">
	<div class="pencipdc_dock_player_inner">
        <?php if ( get_theme_mod( 'pencipodcast_podcast_hide_button' ) ) : 
            $pos = get_theme_mod( 'pencipodcast_podcast_hide_pos', 'left' );
            ?>
            <div class="pencipdc_dock_player_button <?php echo $pos; ?>">
                <span onclick="document.getElementsByClassName('pencipdc_dock_player')[0].classList.toggle('toggle-hide')"><?php echo pencipdc_translate( 'sh'); ?></span>
            </div>
        <?php endif; ?>
		<?php echo do_shortcode( '[pencipdc_player]' ); ?>
	</div>
	<script>
        var initial_player = localStorage.getItem('pencipdc_player'),
            initial_player = null !== initial_player && 0 < initial_player.length ? JSON.parse(localStorage.getItem('pencipdc_player')) : {},
            playlist = 'undefined' !== typeof initial_player.playlist ? initial_player.playlist.length != 0 : false

        if (playlist) {
            document.getElementsByClassName('pencipdc_dock_player')[0].classList.add('show')
        }
	</script>
</div>