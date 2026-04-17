module.exports = {
    env: {
        browser: true,
        es6: true
    },
    extends: ['plugin:react/recommended', 'standard'],
    settings: {
        react: {
            version: 'detect'
        }
    },
    rules: {
        indent: ['error', 4],
        'no-new': 'off',
        'no-undef': 'off',
        'comma-dangle': 'off',
        'react/prop-types': 'off',
        'react/react-in-jsx-scope': 'off',
    }
}
