const path = require('path')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')

const isDevelopment = process.env.ENV !== 'production'
const isWatching = process.env.WATCH !== 'false'

module.exports = {
  mode: isDevelopment ? 'development' : 'production',
  watch: isWatching,
  entry: {
    front: './assets/src/js/front.js',
    admin: './assets/src/js/admin.js'
  },
  output: {
    filename: isDevelopment ? 'js/[name].js' : 'js/[name].[contenthash].js',
    path: path.resolve(__dirname, './dist'),
    assetModuleFilename: 'static/[name].[contenthash][ext][query]',
    clean: true
  },
  externals: {
    jquery: 'jQuery'
  },
  devtool: isDevelopment ? 'eval-source-map' : 'source-map',
  plugins: [
    new MiniCssExtractPlugin({
      filename: isDevelopment ? 'css/[name].css' : 'css/[name].[contenthash].css'
    })
  ],
  module: {
    rules: [
      {
        // Javascript
        test: /\.jsx?$/,
        use: 'babel-loader',
        exclude: /[\\/]node_modules[\\/](?!(algoliasearch)[\\/])/
      }, {
        // (S)CSS
        test: /\.(sa|sc|c)ss$/,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
            options: { sourceMap: isDevelopment }
          }, {
            loader: 'postcss-loader',
            options: { sourceMap: isDevelopment }
          }, {
            loader: 'sass-loader',
            options: { sourceMap: isDevelopment }
          }
        ]
      }, {
        // Images
        test: /\.(png|svg|jpg|jpeg|gif)$/i,
        type: 'asset/resource',
        generator: {
          filename: 'images/[name].[contenthash][ext][query]'
        }
      },
      {
        // Fonts
        test: /\.(woff|woff2|eot|ttf|otf)$/i,
        type: 'asset/resource',
        generator: {
          filename: 'fonts/[name].[contenthash][ext][query]'
        }
      }
    ]
  },
  // @see https://webpack.js.org/configuration/resolve/#resolveextensions
  resolve: {
    extensions: ['.js', '.jsx', '.scss'],
    fallback: {
      http: false,
      https: false,
      url: false
    }
  }
}
