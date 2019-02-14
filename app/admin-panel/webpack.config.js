const VueLoaderPlugin = require('vue-loader/lib/plugin');

module.exports = {
	entry: './src/main.js',

	output: {
		path: __dirname + '/dist/',
		filename: 'app.js',
	},

	module: {
		rules: [
			{
				test: /\.vue$/,
				loader: 'vue-loader',
			},
			{
				test: /\.js$/,
				exclude: /(node_modules|bower_components)/,
				loader: 'babel-loader',
				options: {
					presets: ['@babel/preset-env']
				}
			},
			{
				test: /\.scss$/,
				use: [
					{loader: 'style-loader'},
					{loader: 'css-loader'},
					{loader: 'sass-loader'}
				]
			},
			{
				test: /\.svg$/,
				loader: 'vue-svg-loader',
				options: {
					// Disable SVGO optimisation because it messess up SVGs.
					svgo: false,
				}
			},
		]
	},

	plugins: [
		// Required by vue-loader
		new VueLoaderPlugin(),
	],
}