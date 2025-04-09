# ChordPro

A web-based ChordPro editor and previewer built with CodeIgniter 4.

## Features

- Real-time ChordPro format editing and preview
- Chord transposition with key changes
- Automatic preview updates
- Clean, modern interface
- Responsive design

## Requirements

- PHP 7.4 or higher
- CodeIgniter 4
- Composer
- Web server (Apache/Nginx)
- MySQL/MariaDB database
- Required PHP extensions:
  - [intl](http://php.net/manual/en/intl.requirements.php)
  - [mbstring](http://php.net/manual/en/mbstring.installation.php)
  - [mysqlnd](http://php.net/manual/en/mysqlnd.install.php)
  - json (enabled by default)

> [!WARNING]
> The end of life date for PHP 7.4 was November 28, 2022.
> The end of life date for PHP 8.0 was November 26, 2023.
> If you are still using PHP 7.4 or 8.0, you should upgrade immediately.
> The end of life date for PHP 8.1 will be November 25, 2024.

## Installation

1. Clone the repository:
```bash
git clone https://github.com/phpjeff/Chordpro.git
cd Chordpro
```

2. Install dependencies:
```bash
composer install
```

3. Configure your environment:
- Copy `env` to `.env`
- Update the base URL and database settings in `.env`
- Configure your web server to point to the `public` directory

## Usage

1. Access the application through your web browser
2. Enter your song title and metadata
3. Write your lyrics with ChordPro format chords
4. Use the transpose feature to change keys
5. Preview updates in real-time

## ChordPro Format

The editor supports the following ChordPro format elements:

### Chords
- Inline chords in square brackets: `[C]Here are [Am]the lyrics`
- Chord progression lines with bars: `| C | Am | F | G |`

### Section Markers
- Verse: `Verse 1:`, `Verse 2:`, etc.
- Chorus: `Chorus:`, `Chorus 1:`, `Chorus 2:`
- Bridge: `Bridge:`
- Pre-Chorus: `Pre-Chorus:`
- Post-Chorus: `Post-Chorus:`
- Intro: `Intro:`
- Outro: `Outro:`
- Tag: `Tag:`
- Interlude: `Interlude:`
- Turnaround: `Turnaround:`
- Breakdown: `Breakdown:`
- Break: `Break:`
- Instrumental: `Instrumental:`
- Solo: `Solo:`

### Metadata Directives
- Title: `{title: Song Title}`
- Artist: `{artist: Artist Name}` or `{meta: artist Artist Name}`
- Key: `{key: C}`
- Tempo: `{tempo: 120}` (in BPM)
- Time Signature: `{time: 4/4}`
- Copyright: `{copyright: (c) 2024}` or `{meta: copyright (c) 2024}`
- CCLI Song Number: `{ccli: 12345}`
- CCLI License: `{ccli_license: 12345}`
- Capo: `{capo: 2}` or `{meta: capo 2}`
- Header: `{header: Custom Header}` or `{meta: header Custom Header}`
- Footer: `{footer: Custom Footer}` or `{meta: footer Custom Footer}`

### Page Layout
- Page Break: `{new_page}` or `{np}`

## Important Notes

- `index.php` is located in the *public* folder for better security
- Configure your web server to point to the project's *public* folder
- Do not expose the project root to the web server

## License

MIT License 