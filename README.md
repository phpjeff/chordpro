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
- Copy `.env.example` to `.env`
- Update the base URL and other settings in `.env`

4. Set up your web server to point to the `public` directory

## Usage

1. Access the application through your web browser
2. Enter your song title and metadata
3. Write your lyrics with ChordPro format chords
4. Use the transpose feature to change keys
5. Preview updates in real-time

## ChordPro Format

The editor supports standard ChordPro format:
- Chords in square brackets: `[C]`
- Section markers: `[Verse 1]`, `[Chorus]`, etc.
- Metadata directives: `{title: Song Title}`, `{key: C}`, etc.

## License

MIT License 