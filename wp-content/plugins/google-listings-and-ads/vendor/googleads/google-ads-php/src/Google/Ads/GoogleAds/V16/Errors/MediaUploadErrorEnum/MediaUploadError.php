<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/ads/googleads/v16/errors/media_upload_error.proto

namespace Google\Ads\GoogleAds\V16\Errors\MediaUploadErrorEnum;

use UnexpectedValueException;

/**
 * Enum describing possible media uploading errors.
 *
 * Protobuf type <code>google.ads.googleads.v16.errors.MediaUploadErrorEnum.MediaUploadError</code>
 */
class MediaUploadError
{
    /**
     * Enum unspecified.
     *
     * Generated from protobuf enum <code>UNSPECIFIED = 0;</code>
     */
    const UNSPECIFIED = 0;
    /**
     * The received error code is not known in this version.
     *
     * Generated from protobuf enum <code>UNKNOWN = 1;</code>
     */
    const UNKNOWN = 1;
    /**
     * The uploaded file is too big.
     *
     * Generated from protobuf enum <code>FILE_TOO_BIG = 2;</code>
     */
    const FILE_TOO_BIG = 2;
    /**
     * Image data is unparseable.
     *
     * Generated from protobuf enum <code>UNPARSEABLE_IMAGE = 3;</code>
     */
    const UNPARSEABLE_IMAGE = 3;
    /**
     * Animated images are not allowed.
     *
     * Generated from protobuf enum <code>ANIMATED_IMAGE_NOT_ALLOWED = 4;</code>
     */
    const ANIMATED_IMAGE_NOT_ALLOWED = 4;
    /**
     * The image or media bundle format is not allowed.
     *
     * Generated from protobuf enum <code>FORMAT_NOT_ALLOWED = 5;</code>
     */
    const FORMAT_NOT_ALLOWED = 5;
    /**
     * Cannot reference URL external to the media bundle.
     *
     * Generated from protobuf enum <code>EXTERNAL_URL_NOT_ALLOWED = 6;</code>
     */
    const EXTERNAL_URL_NOT_ALLOWED = 6;
    /**
     * HTML5 ad is trying to reference an asset not in .ZIP file.
     *
     * Generated from protobuf enum <code>INVALID_URL_REFERENCE = 7;</code>
     */
    const INVALID_URL_REFERENCE = 7;
    /**
     * The media bundle contains no primary entry.
     *
     * Generated from protobuf enum <code>MISSING_PRIMARY_MEDIA_BUNDLE_ENTRY = 8;</code>
     */
    const MISSING_PRIMARY_MEDIA_BUNDLE_ENTRY = 8;
    /**
     * Animation has disallowed visual effects.
     *
     * Generated from protobuf enum <code>ANIMATED_VISUAL_EFFECT = 9;</code>
     */
    const ANIMATED_VISUAL_EFFECT = 9;
    /**
     * Animation longer than the allowed 30 second limit.
     *
     * Generated from protobuf enum <code>ANIMATION_TOO_LONG = 10;</code>
     */
    const ANIMATION_TOO_LONG = 10;
    /**
     * The aspect ratio of the image does not match the expected aspect ratios
     * provided in the asset spec.
     *
     * Generated from protobuf enum <code>ASPECT_RATIO_NOT_ALLOWED = 11;</code>
     */
    const ASPECT_RATIO_NOT_ALLOWED = 11;
    /**
     * Audio files are not allowed in bundle.
     *
     * Generated from protobuf enum <code>AUDIO_NOT_ALLOWED_IN_MEDIA_BUNDLE = 12;</code>
     */
    const AUDIO_NOT_ALLOWED_IN_MEDIA_BUNDLE = 12;
    /**
     * CMYK jpegs are not supported.
     *
     * Generated from protobuf enum <code>CMYK_JPEG_NOT_ALLOWED = 13;</code>
     */
    const CMYK_JPEG_NOT_ALLOWED = 13;
    /**
     * Flash movies are not allowed.
     *
     * Generated from protobuf enum <code>FLASH_NOT_ALLOWED = 14;</code>
     */
    const FLASH_NOT_ALLOWED = 14;
    /**
     * The frame rate of the video is higher than the allowed 5fps.
     *
     * Generated from protobuf enum <code>FRAME_RATE_TOO_HIGH = 15;</code>
     */
    const FRAME_RATE_TOO_HIGH = 15;
    /**
     * ZIP file from Google Web Designer is not published.
     *
     * Generated from protobuf enum <code>GOOGLE_WEB_DESIGNER_ZIP_FILE_NOT_PUBLISHED = 16;</code>
     */
    const GOOGLE_WEB_DESIGNER_ZIP_FILE_NOT_PUBLISHED = 16;
    /**
     * Image constraints are violated, but more details (like
     * DIMENSIONS_NOT_ALLOWED or ASPECT_RATIO_NOT_ALLOWED) can not be provided.
     * This happens when asset spec contains more than one constraint and
     * criteria of different constraints are violated.
     *
     * Generated from protobuf enum <code>IMAGE_CONSTRAINTS_VIOLATED = 17;</code>
     */
    const IMAGE_CONSTRAINTS_VIOLATED = 17;
    /**
     * Media bundle data is unrecognizable.
     *
     * Generated from protobuf enum <code>INVALID_MEDIA_BUNDLE = 18;</code>
     */
    const INVALID_MEDIA_BUNDLE = 18;
    /**
     * There was a problem with one or more of the media bundle entries.
     *
     * Generated from protobuf enum <code>INVALID_MEDIA_BUNDLE_ENTRY = 19;</code>
     */
    const INVALID_MEDIA_BUNDLE_ENTRY = 19;
    /**
     * The asset has an invalid mime type.
     *
     * Generated from protobuf enum <code>INVALID_MIME_TYPE = 20;</code>
     */
    const INVALID_MIME_TYPE = 20;
    /**
     * The media bundle contains an invalid asset path.
     *
     * Generated from protobuf enum <code>INVALID_PATH = 21;</code>
     */
    const INVALID_PATH = 21;
    /**
     * Image has layout problem.
     *
     * Generated from protobuf enum <code>LAYOUT_PROBLEM = 22;</code>
     */
    const LAYOUT_PROBLEM = 22;
    /**
     * An asset had a URL reference that is malformed per RFC 1738 convention.
     *
     * Generated from protobuf enum <code>MALFORMED_URL = 23;</code>
     */
    const MALFORMED_URL = 23;
    /**
     * The uploaded media bundle format is not allowed.
     *
     * Generated from protobuf enum <code>MEDIA_BUNDLE_NOT_ALLOWED = 24;</code>
     */
    const MEDIA_BUNDLE_NOT_ALLOWED = 24;
    /**
     * The media bundle is not compatible with the asset spec product type.
     * (For example, Gmail, dynamic remarketing, etc.)
     *
     * Generated from protobuf enum <code>MEDIA_BUNDLE_NOT_COMPATIBLE_TO_PRODUCT_TYPE = 25;</code>
     */
    const MEDIA_BUNDLE_NOT_COMPATIBLE_TO_PRODUCT_TYPE = 25;
    /**
     * A bundle being uploaded that is incompatible with multiple assets for
     * different reasons.
     *
     * Generated from protobuf enum <code>MEDIA_BUNDLE_REJECTED_BY_MULTIPLE_ASSET_SPECS = 26;</code>
     */
    const MEDIA_BUNDLE_REJECTED_BY_MULTIPLE_ASSET_SPECS = 26;
    /**
     * The media bundle contains too many files.
     *
     * Generated from protobuf enum <code>TOO_MANY_FILES_IN_MEDIA_BUNDLE = 27;</code>
     */
    const TOO_MANY_FILES_IN_MEDIA_BUNDLE = 27;
    /**
     * Google Web Designer not created for "Google Ads" environment.
     *
     * Generated from protobuf enum <code>UNSUPPORTED_GOOGLE_WEB_DESIGNER_ENVIRONMENT = 28;</code>
     */
    const UNSUPPORTED_GOOGLE_WEB_DESIGNER_ENVIRONMENT = 28;
    /**
     * Unsupported HTML5 feature in HTML5 asset.
     *
     * Generated from protobuf enum <code>UNSUPPORTED_HTML5_FEATURE = 29;</code>
     */
    const UNSUPPORTED_HTML5_FEATURE = 29;
    /**
     * URL in HTML5 entry is not SSL compliant.
     *
     * Generated from protobuf enum <code>URL_IN_MEDIA_BUNDLE_NOT_SSL_COMPLIANT = 30;</code>
     */
    const URL_IN_MEDIA_BUNDLE_NOT_SSL_COMPLIANT = 30;
    /**
     * Video file name is longer than the 50 allowed characters.
     *
     * Generated from protobuf enum <code>VIDEO_FILE_NAME_TOO_LONG = 31;</code>
     */
    const VIDEO_FILE_NAME_TOO_LONG = 31;
    /**
     * Multiple videos with same name in a bundle.
     *
     * Generated from protobuf enum <code>VIDEO_MULTIPLE_FILES_WITH_SAME_NAME = 32;</code>
     */
    const VIDEO_MULTIPLE_FILES_WITH_SAME_NAME = 32;
    /**
     * Videos are not allowed in media bundle.
     *
     * Generated from protobuf enum <code>VIDEO_NOT_ALLOWED_IN_MEDIA_BUNDLE = 33;</code>
     */
    const VIDEO_NOT_ALLOWED_IN_MEDIA_BUNDLE = 33;
    /**
     * This type of media cannot be uploaded through the Google Ads API.
     *
     * Generated from protobuf enum <code>CANNOT_UPLOAD_MEDIA_TYPE_THROUGH_API = 34;</code>
     */
    const CANNOT_UPLOAD_MEDIA_TYPE_THROUGH_API = 34;
    /**
     * The dimensions of the image are not allowed.
     *
     * Generated from protobuf enum <code>DIMENSIONS_NOT_ALLOWED = 35;</code>
     */
    const DIMENSIONS_NOT_ALLOWED = 35;

    private static $valueToName = [
        self::UNSPECIFIED => 'UNSPECIFIED',
        self::UNKNOWN => 'UNKNOWN',
        self::FILE_TOO_BIG => 'FILE_TOO_BIG',
        self::UNPARSEABLE_IMAGE => 'UNPARSEABLE_IMAGE',
        self::ANIMATED_IMAGE_NOT_ALLOWED => 'ANIMATED_IMAGE_NOT_ALLOWED',
        self::FORMAT_NOT_ALLOWED => 'FORMAT_NOT_ALLOWED',
        self::EXTERNAL_URL_NOT_ALLOWED => 'EXTERNAL_URL_NOT_ALLOWED',
        self::INVALID_URL_REFERENCE => 'INVALID_URL_REFERENCE',
        self::MISSING_PRIMARY_MEDIA_BUNDLE_ENTRY => 'MISSING_PRIMARY_MEDIA_BUNDLE_ENTRY',
        self::ANIMATED_VISUAL_EFFECT => 'ANIMATED_VISUAL_EFFECT',
        self::ANIMATION_TOO_LONG => 'ANIMATION_TOO_LONG',
        self::ASPECT_RATIO_NOT_ALLOWED => 'ASPECT_RATIO_NOT_ALLOWED',
        self::AUDIO_NOT_ALLOWED_IN_MEDIA_BUNDLE => 'AUDIO_NOT_ALLOWED_IN_MEDIA_BUNDLE',
        self::CMYK_JPEG_NOT_ALLOWED => 'CMYK_JPEG_NOT_ALLOWED',
        self::FLASH_NOT_ALLOWED => 'FLASH_NOT_ALLOWED',
        self::FRAME_RATE_TOO_HIGH => 'FRAME_RATE_TOO_HIGH',
        self::GOOGLE_WEB_DESIGNER_ZIP_FILE_NOT_PUBLISHED => 'GOOGLE_WEB_DESIGNER_ZIP_FILE_NOT_PUBLISHED',
        self::IMAGE_CONSTRAINTS_VIOLATED => 'IMAGE_CONSTRAINTS_VIOLATED',
        self::INVALID_MEDIA_BUNDLE => 'INVALID_MEDIA_BUNDLE',
        self::INVALID_MEDIA_BUNDLE_ENTRY => 'INVALID_MEDIA_BUNDLE_ENTRY',
        self::INVALID_MIME_TYPE => 'INVALID_MIME_TYPE',
        self::INVALID_PATH => 'INVALID_PATH',
        self::LAYOUT_PROBLEM => 'LAYOUT_PROBLEM',
        self::MALFORMED_URL => 'MALFORMED_URL',
        self::MEDIA_BUNDLE_NOT_ALLOWED => 'MEDIA_BUNDLE_NOT_ALLOWED',
        self::MEDIA_BUNDLE_NOT_COMPATIBLE_TO_PRODUCT_TYPE => 'MEDIA_BUNDLE_NOT_COMPATIBLE_TO_PRODUCT_TYPE',
        self::MEDIA_BUNDLE_REJECTED_BY_MULTIPLE_ASSET_SPECS => 'MEDIA_BUNDLE_REJECTED_BY_MULTIPLE_ASSET_SPECS',
        self::TOO_MANY_FILES_IN_MEDIA_BUNDLE => 'TOO_MANY_FILES_IN_MEDIA_BUNDLE',
        self::UNSUPPORTED_GOOGLE_WEB_DESIGNER_ENVIRONMENT => 'UNSUPPORTED_GOOGLE_WEB_DESIGNER_ENVIRONMENT',
        self::UNSUPPORTED_HTML5_FEATURE => 'UNSUPPORTED_HTML5_FEATURE',
        self::URL_IN_MEDIA_BUNDLE_NOT_SSL_COMPLIANT => 'URL_IN_MEDIA_BUNDLE_NOT_SSL_COMPLIANT',
        self::VIDEO_FILE_NAME_TOO_LONG => 'VIDEO_FILE_NAME_TOO_LONG',
        self::VIDEO_MULTIPLE_FILES_WITH_SAME_NAME => 'VIDEO_MULTIPLE_FILES_WITH_SAME_NAME',
        self::VIDEO_NOT_ALLOWED_IN_MEDIA_BUNDLE => 'VIDEO_NOT_ALLOWED_IN_MEDIA_BUNDLE',
        self::CANNOT_UPLOAD_MEDIA_TYPE_THROUGH_API => 'CANNOT_UPLOAD_MEDIA_TYPE_THROUGH_API',
        self::DIMENSIONS_NOT_ALLOWED => 'DIMENSIONS_NOT_ALLOWED',
    ];

    public static function name($value)
    {
        if (!isset(self::$valueToName[$value])) {
            throw new UnexpectedValueException(sprintf(
                    'Enum %s has no name defined for value %s', __CLASS__, $value));
        }
        return self::$valueToName[$value];
    }


    public static function value($name)
    {
        $const = __CLASS__ . '::' . strtoupper($name);
        if (!defined($const)) {
            throw new UnexpectedValueException(sprintf(
                    'Enum %s has no value defined for name %s', __CLASS__, $name));
        }
        return constant($const);
    }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MediaUploadError::class, \Google\Ads\GoogleAds\V16\Errors\MediaUploadErrorEnum_MediaUploadError::class);

