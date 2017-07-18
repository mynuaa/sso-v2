var path = require('path');
var webpack = require('webpack');

module.exports = {
    entry: ['./src/main.js'],
    output: {
        path: path.resolve(__dirname, './dist'),
        publicPath: '/dist/',
        filename: 'build.js'
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                loader: 'babel-loader',
                exclude: /node_modules/
            },
            {
                test: /\.json$/,
                loader: 'json-loader'
            },
            {
                test: /\.(png|jpg|gif|svg)$/,
                loader: 'file-loader',
                query: {
                    name: 'img/[name].[ext]?[hash]'
                }
            },
            {
                test: /\.css$/,
                loader: 'css-loader'
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                options: {
                    loaders: {
                        js: 'babel-loader!eslint-loader',
                        css: 'sass-loader'
                    }
                }
            }
        ]
    },

    devServer: {
        historyApiFallback: true,
        noInfo: true,
        proxy: {
            '/api': {
                target: 'http://test.my.nuaa.edu.cn/sso-v2',
                changeOrigin: true
            },
            '/ucenter': {
                target: 'http://test.my.nuaa.edu.cn',
                changeOrigin: true
            }
        }
    },
    devtool: '#eval-source-map'
};

if (process.env.NODE_ENV === 'production') {
    module.exports.output.publicPath = '/sso-v2/dist/';
    module.exports.devtool = '#source-map';
    // http://vue-loader.vuejs.org/en/workflow/production.html
    module.exports.plugins = (module.exports.plugins || []).concat([
        new webpack.DefinePlugin({
            'process.env': {
                NODE_ENV: '"production"'
            }
        }),
        new webpack.optimize.UglifyJsPlugin({
            compress: {
                warnings: false
            }
        })
    ]);
}
