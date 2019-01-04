<?php
/**
 * Main plugin functionality.
 *
 * @package SimpleGoogleAds
 */

namespace SimpleGoogleAds;

use ArrayIterator;
use CachingIterator;
use WP_Post;
use WP_Term;

/**
 * Initializes the plugin.
 */
function bootstrap() {
	add_action( 'init', __NAMESPACE__ . '\load_textdomain' );
	add_action( 'init', __NAMESPACE__ . '\register_editor_assets' );
	add_action( 'init', __NAMESPACE__ . '\register_block_types' );
	add_action( 'init', __NAMESPACE__ . '\add_shortcode' );

	add_action( 'wp_head', __NAMESPACE__ . '\print_ad_manager_ads_code' );

	add_action( 'simple-google-ads.ad_tag', __NAMESPACE__ . '\print_ad_tag' );

	add_action( 'widgets_init', __NAMESPACE__ . '\register_widget' );
}

/**
 * Loads translations.
 */
function load_textdomain(): void {
	load_plugin_textdomain(
		'simple-google-ads',
		false,
		\dirname( plugin_basename( __DIR__ ) ) . '/languages'
	);
}

/**
 * Registers JavaScript and CSS for the block editor.
 */
function register_editor_assets(): void {
	if ( ! \function_exists( 'register_block_type' ) ) {
		return;
	}

	wp_register_script(
		'simple-google-ads',
		plugins_url( 'assets/js/editor.js', __DIR__ ),
		[
			'wp-blocks',
			'wp-components',
			'wp-data',
			'wp-edit-post',
			'wp-editor',
			'wp-element',
			'wp-i18n',
			'wp-plugins',
		],
		'20181020',
		true
	);

	wp_register_style(
		'simple-google-ads',
		plugins_url( 'assets/css/editor.css', __DIR__ ),
		[],
		'20181019'
	);

	if ( \function_exists( 'wp_set_script_translations' ) ) {
		wp_set_script_translations( 'simple-google-ads', 'simple-google-ads', \dirname( __DIR__ ) . '/languages' );
	}

	wp_localize_script(
		'simple-google-ads',
		'SimpleGoogleAdsData',
		[
			'tags' => wp_list_pluck( get_ad_tags(), 'name', 'tag' ),
		]
	);
}

/**
 * Registers the custom block types for server side rendering.
 */
function register_block_types(): void {
	if ( ! \function_exists( 'register_block_type' ) ) {
		return;
	}

	register_block_type(
		'simple-google-ads/ad',
		[
			'render_callback' => __NAMESPACE__ . '\render_ad_block',
			'editor_script'   => 'simple-google-ads',
			'editor_style'    => 'simple-google-ads',
		]
	);
}

/**
 * Registers a [simple-google-ads-ad-tag] shortcode to display ads.
 */
function add_shortcode(): void {
	\add_shortcode( 'simple-google-ads-ad-tag', __NAMESPACE__ . '\render_shortcode' );
}

/**
 * Registers a custom widget to display ads.
 */
function register_widget(): void {
	\register_widget( new Widget() );
}

/**
 * Renders the custom shortcode to display a single ad.
 *
 * @param array|mixed $attributes Shortcode attributes.
 *
 * @return string The rendered ad.
 */
function render_shortcode( $attributes ): string {
	if ( empty( $attributes['id'] ) ) {
		return '';
	}

	return render_ad_block( [ 'tag' => $attributes['id'] ] );
}

/**
 * Renders the custom block to display a single ad.
 *
 * @param array $attributes Block attributes.
 * @return string The rendered ad.
 */
function render_ad_block( array $attributes ): string {
	if ( empty( $attributes ) ) {
		return '';
	}

	$defaults = [
		'tag' => '',
	];

	$args = wp_parse_args(
		$attributes,
		$defaults
	);

	if ( empty( $args['tag'] ) ) {
		return '';
	}

	ob_start();

	/**
	 * Fires when displaying a specific ad tag.
	 *
	 * @param string $ad_tag Ad tag name.
	 */
	do_action( 'simple-google-ads.ad_tag', $args['tag'] );

	return ob_get_clean();
}

/**
 * Are we currently on an AMP URL?
 *
 * Will always return `false` if called before the `parse_query` hook.
 *
 * @return bool Whether it is the AMP endpoint.
 */
function is_amp_endpoint(): bool {
	$is_amp_endpoint = \function_exists( '\is_amp_endpoint' ) && \is_amp_endpoint();

	/**
	 * Filters whether the current request is an AMP one or not.
	 *
	 * @param bool $is_amp_endpoint Whether this is an AMP request or not.
	 */
	return apply_filters( 'simple-google-ads.is_amp_endpoint', $is_amp_endpoint );
}

/**
 * Returns the Google Ad Manager account ID.
 *
 * @return int Google Ad Manager account ID.
 */
function get_ad_manager_account_id(): int {
	/**
	 * Filters the Google Ad Manager account ID.
	 *
	 * @param int $account_id Account ID.
	 */
	return apply_filters( 'simple-google-ads.ad_manager_account_id', 0 );
}


/**
 * Returns the ad code for a specific tag name.
 *
 * Supports multiple sizes for an ad.
 *
 * @param string $tag_name Ad Manager tag name.
 * @return null|string Ad code or null if not found.
 */
function get_ad_code( string $tag_name ): ?string {
	$ad_manager_id = get_ad_manager_account_id();

	$found = array_filter( get_ad_tags(), function ( $el ) use ( $tag_name ) {
		return $el['tag'] === $tag_name;
	} );

	if ( ! $found ) {
		return null;
	}

	$tag = array_pop( $found );

	/**
	 * Filters the ad tag name before it's used in markup.
	 *
	 * @param string $tag_name Ad tag name.
	 */
	$tag_name = apply_filters( 'simple-google-ads.ad_tag_name', $tag_name );

	$var = sprintf( 'simple_google_ads_ad_map_%s', $tag_name );

	if ( empty( $tag['sizes'] ) ) {
		ob_start();
		?>
		googletag.defineSlot('/<?php echo absint( $ad_manager_id ); ?>/<?php echo esc_attr( $tag_name ); ?>',
		<?php echo json_encode( [ $tag['size'] ] ); ?>,
		'ad-tag-<?php echo esc_attr( $tag_name ); ?>').
		addService(googletag.pubads());
		<?php
		return ob_get_clean();
	}

	ob_start();
	?>
	var <?php echo esc_attr( $var ); ?> = googletag.sizeMapping().
	<?php foreach ( $tag['sizes'] as $breakpoint => $sizes ) : ?>
		addSize(<?php echo json_encode( array_map( '\intval', explode( ',', $breakpoint ) ) ); ?>, <?php echo esc_attr( json_encode( $sizes ) ); ?>).
	<?php endforeach; ?>
	build();
	adSlots.push(
	googletag.defineSlot('/<?php echo absint( $ad_manager_id ); ?>/<?php echo esc_attr( $tag_name ); ?>',
	<?php echo json_encode( [ $tag['size'] ] ); ?>,
	'ad-tag-<?php echo esc_attr( $tag_name ); ?>'
	).
	defineSizeMapping( <?php echo esc_attr( $var ); ?> ).
	addService( googletag.pubads() ) );
	<?php

	return ob_get_clean();
}

/**
 * Returns the available ad tags.
 *
 * A single ad tag definition should have the following format:
 *
 * [
 *   'tag'   => 'my_tag_name',
 *   'size'  => [ 970, 90 ],
 *   'sizes' => [
 *     '0,0'     => [],
 *     '320,200' => [ [ 320, 50 ], [ 250, 250 ] ],
 *     '768,200' => [ [ 728, 90 ] ],
 *     '990,200' => [ [ 950, 120 ], [ 970, 90 ] ],
 *   ],
 * ],
 *
 * @return array
 */
function get_ad_tags(): array {
	/**
	 * Filters the available ad tags.
	 *
	 * @param array $ad_tags Available ad tags.
	 */
	return apply_filters( 'simple-google-ads.ad_tags', [] );
}

/**
 * Prints the setup code for Google Ad Manager ads.
 *
 * Does nothing on AMP requests, as the AMP plugin requires the necessary script already.
 */
function print_ad_manager_ads_code(): void {
	if ( is_amp_endpoint() ) {
		return;
	}
	?>
	<script src="https://www.googletagservices.com/tag/js/gpt.js"></script>
	<script type='text/javascript'>
		var googletag = googletag || { cmd: [] };
		var adSlots   = [];
		var resizeTimer;

		googletag.cmd.push( function () {
			function resizeAds() {
				googletag.pubads().refresh( adSlots );
			}

			// Refresh adds on resize, debounced.
			window.addEventListener( 'resize', function () {
				clearTimeout( resizeTimer );
				resizeTimer = setTimeout( resizeAds, 250 );
			} );
		} );

		googletag.cmd.push( function () {
			<?php foreach ( get_ad_targeting_data() as $key => $value ) : ?>
			googletag.pubads().setTargeting( '<?php echo esc_js( $key ); ?>', <?php echo wp_json_encode( $value ); ?> );
			<?php endforeach; ?>

			googletag.pubads().collapseEmptyDivs();
			googletag.enableServices();
		} );

		<?php foreach ( get_ad_tags() as $tag ) : ?>
		googletag.cmd.push( function () {
			<?php echo get_ad_code( $tag['tag'] ); ?>
		} );
		<?php endforeach; ?>
	</script>
	<?php
}

/**
 * Returns a list of term names for a given taxonomy and post.
 *
 * @param  string       $taxonomy Taxonomy slug.
 * @param \WP_Post|null $post     Optional. Post object. Defaults to global post.
 * @return array List of term names for the given taxonomy.
 */
function get_term_list( string $taxonomy, WP_Post $post = null ): array {
	$post = get_post( $post );

	if ( ! $post ) {
		return [];
	}

	$terms = get_the_terms( $post->ID, $taxonomy );

	if ( ! $terms || is_wp_error( $terms ) ) {
		return [];
	}

	return array_map(
		function ( WP_Term $term ) {
			return $term->name;
		},
		$terms
	);
}

/**
 * Returns a list of ad targeting data.
 *
 * @return array Ad targeting data.
 */
function get_ad_targeting_data(): array {
	$targeting = [];

	$post = get_post();

	if ( is_search() ) {
		$targeting['searchterm'] = get_search_query();
		$targeting['page']       = 'search';
	}

	if ( is_singular() ) {
		$targeting['page']     = 'singular';
		$targeting['type']     = get_post_type();
		$targeting['id']       = get_the_ID();
		$targeting['title']    = get_the_title();
		$targeting['author']   = get_the_author_meta( 'display_name', $post ? $post->post_author : false );
		$targeting['category'] = implode( ',', get_term_list( 'category' ) );
		$targeting['tag']      = implode( ',', get_term_list( 'post_tag' ) );
	}

	if ( is_front_page() ) {
		$targeting['page'] = 'front';
	}

	if ( is_archive() ) {
		$targeting['page'] = 'archive';
		$targeting['type'] = get_post_type();
	}

	if ( is_category() ) {
		$targeting['page']  = 'category';
		$targeting['title'] = single_cat_title( '', false );
	}

	if ( is_tag() ) {
		$targeting['page']  = 'tag';
		$targeting['title'] = single_tag_title( '', false );
	}

	/**
	 * Filters the ad targeting data before it is returned.
	 *
	 * @param array $targeting Ad targeting data.
	 */
	return apply_filters( 'simple-google-ads.ad_targeting_data', $targeting );
}

/**
 * Prints the HTML markup for a single ad tag.
 *
 * Supports regular ad display as well as AMP multi-size ads.
 *
 * Note that multi-size slots may have unexpected interactions with layout="responsive".
 * For this reason it is strongly encouraged that multi-size slots use layout="fixed".
 *
 * AMP forcefully overrides layout to `fixed`.
 *
 * @param string $tag_name Ad tag name.
 */
function print_ad_tag( string $tag_name ): void {
	/**
	 * Filters the ad tag markup before it is generated.
	 *
	 * Returning anything else than null short-circuits ad tag markup generation.
	 *
	 * @param string $ad_tag   Ad tag markup.
	 * @param string $tag_name Ad tag name.
	 */
	$ad_tag = apply_filters( 'simple-google-ads.pre_ad_tag_markup', null, $tag_name );

	if ( null !== $ad_tag ) {
		echo (string) $ad_tag;

		return;
	}

	$found = array_filter( get_ad_tags(), function ( $el ) use ( $tag_name ) {
		return $el['tag'] === $tag_name;
	} );

	if ( ! $found ) {
		return;
	}

	$tag           = array_pop( $found );
	$ad_manager_id = get_ad_manager_account_id();

	/** This filter is documented in inc/namespace.php */
	$tag_name = apply_filters( 'simple-google-ads.ad_tag_name', $tag_name );

	ob_start();
	if ( is_amp_endpoint() ) :
		$json_data = [ 'targeting' => get_ad_targeting_data() ];
		?>
		<div class='ad-tag' id='<?php echo esc_attr( sprintf( 'ad-tag-%s', $tag_name ) ); ?>'>
			<?php if ( empty( $tag['sizes'] ) ) : ?>
				<amp-ad
					type="doubleclick"
					layout="fixed"
					width="<?php echo absint( $tag['size'][0] ); ?>"
					height="<?php echo absint( $tag['size'][1] ); ?>"
					data-slot="/<?php echo absint( $ad_manager_id ); ?>/<?php echo esc_attr( $tag_name ); ?>"
					json="<?php echo esc_attr( wp_json_encode( $json_data ) ); ?>"
				>
				</amp-ad>
				<?php
			else :
				$iterator = new CachingIterator( new ArrayIterator( $tag['sizes'] ), 0 );

				foreach ( $iterator as $size ) {
					if ( ! $iterator->current() ) {
						continue;
					}

					$min_width_dimensions = explode( ',', $iterator->key() );
					$media_query          = sprintf( '(min-width: %dpx)', (int) $min_width_dimensions[0] );

					if ( $iterator->hasNext() ) {
						$max_width_dimensions = explode( ',', $iterator->getInnerIterator()->key() );

						$media_query .= sprintf( ' and (max-width: %dpx)', ( (int) $max_width_dimensions[0] ) - 1 );
					}

					$multi_sizes = array_map(
						function ( $size ) {
							return sprintf( '%dx%d', $size[0], $size[1] );
						},
						$size
					);

					$max_ad_width = max(
						array_map(
							function ( $size ) {
								return $size[0];
							},
							$size
						)
					);

					$max_ad_height = max(
						array_map(
							function ( $size ) {
								return $size[1];
							},
							$size
						)
					);

					?>
					<amp-ad
						type="doubleclick"
						layout="fixed"
						media="<?php echo esc_attr( $media_query ); ?>"
						width="<?php echo absint( $max_ad_width ); ?>"
						height="<?php echo absint( $max_ad_height ); ?>"
						data-multi-size="<?php echo esc_attr( implode( ',', $multi_sizes ) ); ?>"
						data-slot="/<?php echo absint( $ad_manager_id ); ?>/<?php echo esc_attr( $tag_name ); ?>"
						json="<?php echo esc_attr( wp_json_encode( $json_data ) ); ?>"
					>
					</amp-ad>
					<?php
				}
			endif;
			?>
		</div>
	<?php else : ?>
		<div class='ad-tag'>
			<div class='ad-tag-inner' id='<?php echo esc_attr( sprintf( 'ad-tag-%s', $tag_name ) ); ?>'>
				<script type='text/javascript'>
					googletag.cmd.push( function () {
						googletag.display( '<?php echo esc_attr( sprintf( 'ad-tag-%s', $tag_name ) ); ?>' );
					} );
				</script>
			</div>
		</div>
	<?php
	endif;

	$ad_tag = ob_get_clean();

	/**
	 * Filters the ad tag markup before it is printed.
	 *
	 * @param string $ad_tag Ad tag markup.
	 * @param string $tag_name Ad tag name.
	 */
	echo apply_filters( 'simple-google-ads.ad_tag_markup', $ad_tag, $tag_name );
}
