<?php
$_site_url = site_url();

$use_site_address_url = get_theme_mod( 'penci_amp_use_site_address_url' );
if ( $use_site_address_url ) {
	$_site_url = home_url();
}
?>
<footer class="penci-amp-footer">
    <div class="penci-amp-footer-container">
        <div class="penci-amp-main-link">
			<?php
			$view_desktop_url = esc_attr( Penci_AMP_Link_Sanitizer::__pre_url_off( $_site_url ) );
			if ( get_theme_mod( 'penci_amp_mobile_version' ) ) {
				$view_desktop_url = add_query_arg( array(
					'desktop_view' => 'show',
				), $view_desktop_url );
			}
			?>
            <a href="<?php echo $view_desktop_url; ?>">
                <i class="fa fa-desktop"></i> <?php echo penci_amp_get_setting( 'penci_amp_text_view_desktop' ); ?>
            </a>
        </div>
    </div>
    <div class="footer__copyright_menu">
        <p>
			<?php echo penci_amp_get_setting( 'penci_amp_footer_copy_right' ); ?>
        </p>
        <a href="#top" class="back-to-top"><?php echo penci_amp_get_setting( 'penci_amp_text_backtotop' ); ?><i
                    class="fa  fa-long-arrow-up"></i></a>
    </div>
</footer>
<?php
if ( $ga4_id = penci_amp_get_setting( 'penci-amp-analytics-v4' ) ):
	$ga4_dpe = penci_amp_get_setting( 'penci-amp-analytics-ga4-dpe' );
	$ga4_webv = penci_amp_get_setting( 'penci-amp-analytics-ga4-wvt' );
	$ga4_gce = penci_amp_get_setting( 'penci-amp-analytics-ga4-gce' );
	$ga4_perf = penci_amp_get_setting( 'penci-amp-analytics-ga4-ptt' );
	$penciamp_ga4_config = array(
		'cookies'                  =>
			array(
				'_ga' =>
					array(
						'value' => '$IF(LINKER_PARAM(_gl, _ga),GA1.0.LINKER_PARAM(_gl, _ga),)',
					),
			),
		'linkers'                  =>
			array(
				'_gl' =>
					array(
						'enabled'   => true,
						'ids'       =>
							array(
								'_ga' => '${clientId}',
							),
						'proxyOnly' => false,
					),
			),
		'triggers'                 =>
			array(
				'page_view'         =>
					array(
						'enabled' => $ga4_dpe,
						'on'      => 'visible',
						'request' => 'ga4Pageview',
					),
				'doubleClick'       =>
					array(
						'enabled' => $ga4_dpe,
						'on'      => 'visible',
						'request' => 'ga4Dc',
					),
				'webVitals'         =>
					array(
						'enabled'        => $ga4_webv,
						'on'             => 'timer',
						'timerSpec'      =>
							array(
								'interval'       => 5,
								'maxTimerLength' => 4.99,
								'immediate'      => false,
							),
						'request'        => 'ga4Event',
						'vars'           =>
							array(
								'ga4_event_name' => 'web_vitals',
							),
						'extraUrlParams' =>
							array(
								'event__num_first_contenful_paint'    => 'FIRST_CONTENTFUL_PAINT',
								'event__num_first_viewport_ready'     => 'FIRST_VIEWPORT_READY',
								'event__num_make_body_visible'        => 'MAKE_BODY_VISIBLE',
								'event__num_largest_contentful_paint' => 'LARGEST_CONTENTFUL_PAINT',
								'event__num_cumulative_layout_shift'  => 'CUMULATIVE_LAYOUT_SHIFT',
							),
					),
				'performanceTiming' =>
					array(
						'enabled'        => $ga4_perf,
						'on'             => 'visible',
						'request'        => 'ga4Event',
						'sampleSpec'     =>
							array(
								'sampleOn'  => '${clientId}',
								'threshold' => 100,
							),
						'vars'           =>
							array(
								'ga4_event_name' => 'performance_timing',
							),
						'extraUrlParams' =>
							array(
								'event__num_page_load_time'        => '${pageLoadTime}',
								'event__num_domain_lookup_time'    => '${domainLookupTime}',
								'event__num_tcp_connect_time'      => '${tcpConnectTime}',
								'event__num_redirect_time'         => '${redirectTime}',
								'event__num_server_response_time'  => '${serverResponseTime}',
								'event__num_page_download_time'    => '${pageDownloadTime}',
								'event__num_content_download_time' => '${contentLoadTime}',
								'event__num_dom_interactive_time'  => '${domInteractiveTime}',
							),
					),
			),
		'vars'                     =>
			array(
				'ampHost'          => '${ampdocHost}',
				'documentLocation' => 'SOURCE_URL',
				'clientId'         => 'CLIENT_ID(AMP_ECID_GOOGLE,,_ga,true)',
				'dataSource'       => 'AMP',
			),
		'extraUrlParams'           =>
			array(
				'sid' => '$CALC(SESSION_TIMESTAMP, 1000, divide, true)',
				'sct' => 'SESSION_COUNT',
				'seg' => '$IF($EQUALS(SESSION_ENGAGED, true),1,0)',
				'_et' => '$CALC(TOTAL_ENGAGED_TIME,1000, multiply)',
				'gcs' => '$IF($EQUALS(' . $ga4_gce . ',TRUE),G10$IF($EQUALS(CONSENT_STATE,sufficient),1,0),)',
			),
		'extraUrlParamsReplaceMap' =>
			array(
				'user__str_'  => 'up.',
				'user__num_'  => 'upn.',
				'event__str_' => 'ep.',
				'event__num_' => 'epn.',
			),
		'requestOrigin'            => 'https://www.google-analytics.com',
		'requests'                 =>
			array(
				'ga4IsFirstVisit'   => '$IF($EQUALS($CALC(SESSION_COUNT, $CALC($CALC(${timestamp}, 1000, divide, true),$CALC(SESSION_TIMESTAMP, 1000, divide, true), subtract), add),1), _fv, __nfv )',
				'ga4IsSessionStart' => '$IF($EQUALS($CALC($CALC(${timestamp}, 1000, divide, true),$CALC(SESSION_TIMESTAMP, 1000, divide, true), subtract),0), _ss, __nss)',
				'ga4SharedPayload'  => 'v=2&tid=' . $ga4_id . '&ds=${dataSource}&_p=${pageViewId}&cid=${clientId}&ul=${browserLanguage}&sr=${screenWidth}x${screenHeight}&_s=${requestCount}&dl=${documentLocation}&dr=${externalReferrer}&dt=${title}&${ga4IsFirstVisit}=1&${ga4IsSessionStart}=1',
				'ga4Pageview'       =>
					array(
						'baseUrl' => '/g/collect?${ga4SharedPayload}&en=page_view',
					),
				'ga4Event'          =>
					array(
						'baseUrl' => '/g/collect?${ga4SharedPayload}&en=${ga4_event_name}',
					),
				'ga4Dc'             =>
					array(
						'origin'  => 'https://stats.g.doubleclick.net',
						'baseUrl' => '/g/collect?v=2&tid=' . $ga4_id . '&cid=${clientId}&aip=1',
					),
			),
	);
	$penciamp_ga4_fields = json_encode( $penciamp_ga4_config );
	?>
    <amp-analytics type="googleanalytics" data-credentials="include">
        <script type="application/json">
			<?php echo $penciamp_ga4_fields; ?>
        </script>
    </amp-analytics>
<?php
elseif ( $analytics_code = penci_amp_get_setting( 'penci-amp-analytics' ) ) :
	?>
    <amp-analytics type="googleanalytics">
        <script type="application/json">
			{
				"vars": {
					"account": "<?php echo esc_attr( $analytics_code ) ?>"
				},
				"triggers": {
					"trackPageview": {
						"on": "visible",
						"request": "pageview"
					}
				}
			}
        </script>
    </amp-analytics>
<?php endif ?>
<?php
$gprd_desc = $gprd_accept = $gprd_rmore = $gprd_rmore_link = '';
if ( function_exists( 'penci_get_setting' ) ) {
	$gprd_desc       = penci_get_setting( 'penci_gprd_desc' );
	$gprd_accept     = penci_get_setting( 'penci_gprd_btn_accept' );
	$gprd_rmore      = penci_get_setting( 'penci_gprd_rmore' );
	$gprd_rmore_link = penci_get_setting( 'penci_gprd_rmore_link' );
}
if ( get_theme_mod( 'penci_enable_cookie_law' ) && $gprd_desc && $gprd_accept && $gprd_rmore ):
	?>
    <amp-user-notification layout="nodisplay" id="amp-user-notification-gdpr">
        <div class="penci-gprd-law">
            <p>
				<?php if ( $gprd_desc ): echo $gprd_desc; endif; ?>
                <button on="tap:amp-user-notification-gdpr.dismiss"
                        class="ampstart-btn caps ml1 penci-gprd-accept"><?php echo $gprd_accept; ?></button>
				<?php if ( $gprd_rmore ): echo '<a class="penci-gprd-more" href="' . $gprd_rmore_link . '">' . $gprd_rmore . '</a>'; endif; ?>
            </p>
        </div>
    </amp-user-notification>
<?php
endif;
?>
