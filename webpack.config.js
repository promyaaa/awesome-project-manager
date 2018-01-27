// const HtmlWebpackPlugin = require('transform-runtime');
const path = require('path');

module.exports = {
  // This is the "main" file which should include all other modules
  entry: './assets/js/admin.js',
  // Where should the compiled file go?
  output: {
    // To the `dist` folder
    path: path.resolve(__dirname, './assets/js'),
    // With the filename `build.js` so it's dist/build.js

    // path: './dist',
    // publicPath: 'dist/',
    filename: 'build.js'
    // filename: 'build.js'
  },
  // module: {
  //   // Special compilation rules
  //   rules: [
  //     {
  //       // Ask webpack to check: If this file ends with .js, then apply some transforms
  //       test: /\.js$/,
  //       // use: 'raw-loader',
  //       // Transform it with babel
  //       loader: 'babel-loader',
        
  //       // don't transform node_modules folder (which don't need to be compiled)
  //       exclude: /node_modules/
  //     }
  //   ]
  // },
// test code
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