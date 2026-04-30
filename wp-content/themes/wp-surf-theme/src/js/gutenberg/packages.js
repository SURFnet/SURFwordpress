/**
 * Most used WordPress Gutenberg components
 *
 * Add missing ones as needed
 * Keep alphabetically ordered
 */

//
// wp.blockEditor

export const {
    BlockControls,
    ButtonBlockAppender,
    getColorClassName,
    getColorObjectByColorValue,
    InnerBlocks,
    InspectorControls,
    MediaPlaceholder,
    MediaReplaceFlow,
    MediaUpload,
    MediaUploadCheck,
    PanelColorSettings,
    RichText,
    URLInput,
    useBlockProps,
    withColors,
    __experimentalBlockAlignmentMatrixControl,
    __experimentalColorGradientSettingsDropdown,
    __experimentalUseMultipleOriginColorsAndGradients
} = wp.blockEditor

//
// wp.blocks

export const {
    registerBlockStyle,
    registerBlockType,
    unregisterBlockStyle
} = wp.blocks

//
// wp.components

export const {
    BaseControl,
    Button,
    CheckboxControl,
    ColorIndicator,
    ColorPalette,
    Icon,
    Modal,
    Panel,
    PanelBody,
    PanelRow,
    Placeholder,
    QueryControls,
    RadioControl,
    RangeControl,
    SelectControl,
    TextControl,
    ToggleControl,
    __experimentalToggleGroupControl,
    __experimentalToggleGroupControlOption,
} = wp.components

//
// wp.compose

export const {
    withState
} = wp.compose

//
// wp.data

export const {
    dispatch,
    select,
    withSelect,
    useDispatch
} = wp.data

//
// wp.date

export const {
    date
} = wp.date

//
// wp.element

export const {
    Component,
    Fragment,
    useEffect,
    useState,
    useRef,
    useMemo
} = wp.element

//
// wp.data

export const {
    useSelect,
} = wp.data

//
// wp.i18n

export const {
    __,
    _x,
    sprintf
} = wp.i18n

export const {
    addFilter,
    addAction
} = wp.hooks

//
// wp.serverSideRender

export const ServerSideRender = wp.serverSideRender
