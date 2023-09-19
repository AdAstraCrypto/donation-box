const CSSExtract = require('mini-css-extract-plugin');
const NodePolyfillPlugin = require("node-polyfill-webpack-plugin");

module.exports = {
    mode: 'production',
    entry: {
        // 'block-sidebar-plugin': ['./assets/js/block-sidebar-plugin.js'],
        // 'meta-locker-block-editor': ['./assets/js/meta-locker-block-editor.js', './assets/css/meta-locker-block-editor.scss'],
        // 'settings-page': ['./assets/js/settings-page.js'],
        // bundle: ['./assets/js/bundle.js'],
        admin: ['./assets/js/admin.js'],
        // frontend: ['./assets/js/admin.js', './assets/css/frontend.scss'],
    },
    output: {
        path: __dirname,
        filename: 'assets/js/[name].min.js'
    },
    plugins: [
        new NodePolyfillPlugin(),
        new CSSExtract({
            filename: 'assets/css/[name].min.css'
        }),
    ],
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                loader: 'babel-loader'
            },
            {
                test: /\.scss$/,
                use: [
                    CSSExtract.loader,
                    'css-loader',
                    {
                        loader: 'sass-loader',
                        options: {
                            sourceMap: false,
                            sassOptions: {
                                outputStyle: 'compressed'
                            }
                        }
                    }
                ]
            },
            {
                test: /\.(png|jpg|gif|svg)$/i,
                type: 'asset/inline'
            }
        ]
    },
    externals: {
        'lodash': 'lodash',
        '@wordpress/data': 'wp.data',
        '@wordpress/i18n': 'wp.i18n',
        '@wordpress/hooks': 'wp.hooks',
        '@wordpress/blocks': 'wp.blocks',
        '@wordpress/editor': 'wp.editor',
        '@wordpress/plugins': 'wp.plugins',
        '@wordpress/compose': 'wp.compose',
        '@wordpress/element': 'wp.element',
        '@wordpress/api-fetch': 'wp.apiFetch',
        '@wordpress/edit-post': 'wp.editPost',
        '@wordpress/components': 'wp.components',
        '@wordpress/block-editor': 'wp.blockEditor',
        '@wordpress/html-entities': 'wp.htmlEntities'
    },
    watch: true,
    watchOptions: {
        ignored: ['**/*.min.js']
    }
}
