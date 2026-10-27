const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CopyPlugin = require('copy-webpack-plugin');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;


const settings = {
  // The BrowserSync hostname
  host: 'localhost',
  // The port to run BrowserSync's server on
  port: 3000,

  // A target to proxy all BrowserSync requests to.
  proxy: 'http://localhost:10351'
};

module.exports = {
  entry: [
    './src/js/main.js',
    './src/scss/styles.scss'
  ],
  output: {
    filename: "js/bundle.js",
    path: path.resolve(__dirname, 'assets'),
  },
  plugins: [
    // new BundleAnalyzerPlugin(),
    new CopyPlugin({
      patterns: [
        { from: "src/js/vendor", to: "js/vendor" },
        { from: "src/img", to: "img" }
      ],
    }),
    new MiniCssExtractPlugin({
      filename: "css/style.css"
    }),
    new BrowserSyncPlugin(
      {
        host: settings.host,
        port: settings.port,
        proxy: settings.proxy,
        notify: false,
        rewriteRules: false,
        files: [
          {
            match: ['./views/**/*.twig'],
            fn: function (event, file) {
              if (event === "change") {
                const bs = require('browser-sync').get('bs-webpack-plugin');
                bs.reload();
              }
            }
          }
        ]
      },
      {
        name: 'bs-webpack-plugin',
        reload: true
      }
    )
  ],
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader'
        }
      },
      {
        test: /\.(sa|sc|c)ss$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
            options: {
              publicPath: '../'
            }
          },
          {
            loader: "css-loader",
          },
          {
            loader: "sass-loader"
          }
        ]
      },
      {
        test: /\.(woff|woff2|ttf|otf|eot)$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              context: path.resolve(__dirname, 'src'),
              outputPath: 'fonts'
            }
          }
        ]
      }
    ]
  }
};
