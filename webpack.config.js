// const HtmlWebpackPlugin = require('transform-runtime');

const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const path = require('path');

module.exports = {
  plugins: [
    // new UglifyJsPlugin()
  ],
  // This is the "main" file which should include all other modules
  entry: './assets/js/admin.js',
  // Where should the compiled file go?
  output: {
    // To the `dist` folder
    path: path.resolve(__dirname, './assets/dist'),
    // With the filename `build.js` so it's dist/build.js

    // path: './dist',
    // publicPath: 'dist/',
    filename: 'build.js'
    // filename: 'build.js'
  },
    module: {
    rules: [
      {
        test: /\.js$/,
        use: 'babel-loader',
        exclude: /node_modules/
      },
      {
        test: /\.vue$/,
        use: 'vue-loader'
      }
    ]
  },
}