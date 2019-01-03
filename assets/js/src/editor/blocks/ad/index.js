/**
 * WordPress dependencies
 */
import { __, _x, sprintf } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { SelectControl, TextControl } from '@wordpress/components';

/**
 * Internal dependencies
 */
import './editor.css';

const adTags  = window.SimpleGoogleAdsData.tags;
const tagKeys = Object.getOwnPropertyNames( adTags );

registerBlockType( 'simple-google-ads/ad', {
	title: _x( 'Ad ', 'block name', 'simple-google-ads' ),

	description: __( 'Display a single Google Ad Manager ad.', 'simple-google-ads' ),

	icon:
		<svg className="logo" width="112px" height="112px" enableBackground="new 0 0 192 192" version="1.1" viewBox="0 0 192 192" xmlns="http://www.w3.org/2000/svg"><rect width="192" height="192" fill="none" opacity=".4"/><linearGradient id="b" x1="96" x2="12.922" y1="25" y2="108.08" gradientUnits="userSpaceOnUse"><stop stopColor="#1A6DDD" offset="0"/><stop stopColor="#2976E6" offset=".1393"/><stop stopColor="#377FEE" offset=".339"/><stop stopColor="#4084F3" offset=".5838"/><stop stopColor="#4285F4" offset="1"/></linearGradient><path d="M84,13L12.92,84.31c-6.56,6.56-6.56,17.21,0,23.77s17.21,6.56,23.77,0L108,37L84,13z" fill="url(#b)"/><path d="m108.01 37c-6.64 6.64-17.39 6.66-24.03 0.02s-6.62-17.39 0.02-24.04c6.64-6.64 17.39-6.66 24.04-0.02s6.62 17.4-0.03 24.04z" fill="#34A853"/><path d="m179.09 84.04c-6.55-6.56-17.16-6.56-23.71-0.02l-0.01-0.01-71.37 70.99 23.89 24.11 71.22-71.33-0.01-0.01c6.54-6.55 6.53-17.18-0.01-23.73z" fill="#FBBC04"/><circle cx="96" cy="167" r="17" fill="#34A853"/><path d="m144.02 47.99c-6.64-6.65-17.39-6.65-24.03 0-0.14 0.14-0.26 0.29-0.39 0.44l-35.6 35.57 24 24 35.59-35.55c0.14-0.13 0.29-0.25 0.43-0.39 6.64-6.65 6.64-17.43 0-24.07z" fill="#FBBC04"/><linearGradient id="a" x1="95.93" x2="47.948" y1="96.059" y2="144.04" gradientUnits="userSpaceOnUse"><stop stopColor="#1A6DDD" offset="0"/><stop stopColor="#2775E5" offset=".1326"/><stop stopColor="#367EED" offset=".3558"/><stop stopColor="#3F83F2" offset=".6162"/><stop stopColor="#4285F4" offset="1"/></linearGradient><path d="m83.95 84.07l-36.19 36.15c-6.62 6.6-6.36 17.26 0.25 23.87 6.61 6.6 17.08 6.65 23.69 0.05l36.22-36.06-23.97-24.01z" fill="url(#a)"/><path d="m108 108c-6.65 6.65-17.37 6.79-24.02 0.14s-6.63-17.49 0.02-24.14 17.4-6.58 24.05 0.07 6.6 17.29-0.05 23.93z" fill="#34A853"/></svg>,

	category: 'widgets',

	keywords: [
		__( 'ad', 'simple-google-ads' ),
		__( 'google', 'simple-google-ads' ),
	],

	supports: {
		customClassName: false,
		html: false,
		multiple: true,
	},

	attributes: {
		tag: {
			type: 'string',
			default: '',
		},
	},

	transforms: {
		from: [
			{
				type: 'shortcode',
				tag: 'simple-google-ads-ad-tag',
				attributes: {
					tag: {
						type: 'string',
						shortcode: attributes => attributes.named.id || attributes.named.tag || '',
					},
				},
			},
		]
	},

	edit( { className, attributes: { tag }, setAttributes } ) {
		if ( !tag || (tagKeys.length > 0 && tagKeys.indexOf( tag ) > -1) ) {
			return (
				<div className={className}>
					<SelectControl
						label={__( 'Ad tag', 'simple-google-ads' )}
						value={tag}
						onChange={( newValue ) => {
							setAttributes( { tag: newValue } );
						}}
						options={tagKeys.map( tagKey => {
							const tagName = adTags[ tagKey ];

							return {
								value: tagKey,
								label:  tagName ? sprintf( '%1$s (%2$s)', tagName, tagKey ) : tagKey,
							}
						} )}
					/>
				</div>
			);
		}

		return (
			<div className={className}>
				<TextControl
					label={__( 'Ad tag', 'simple-google-ads' )}
					help={__( 'Type the name of the ad tag that you want to display', 'simple-google-ads' )}
					value={tag}
					onChange={( newValue ) => {
						setAttributes( { tag: newValue } );
					}}
				/>
			</div>
		);
	},

	save() {
		// Server side rendering.
		return null;
	},
} );
