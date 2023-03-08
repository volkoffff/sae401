/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./templates/**/*.twig","./node_modules/tw-elements/dist/js/**/*.js"
],
  theme: {
    extend: {},
  },
plugins: [require("node_modules/tw-elements/dist/plugin")],
}


