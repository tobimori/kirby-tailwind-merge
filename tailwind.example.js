module.exports = {
  content: {
    files: ['./site/**/*.php', './src/index.js'],
    transform: (code) => {
      const variantGroupsRegex = /mod\(.([^,"']+)[^\[]+["'](.+)["']\)/g
      const variantGroupMatches = [...code.matchAll(variantGroupsRegex)]

      variantGroupMatches.forEach(([matchStr, variants, classes]) => {
        const parsedClasses = classes
          .split(' ')
          .map((cls) => `${variants}:${cls}`)
          .join(' ')

        code = code.replaceAll(matchStr, parsedClasses)
      })

      return code
    }
  },
  theme: {
    extend: {}
  },
  plugins: []
}
