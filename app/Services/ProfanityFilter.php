<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ProfanityFilter
{
    /**
     * A list of banned words/phrases
     *
     * @var array
     */
    protected $dictionary = [];

    /**
     * Constructor - load the dictionary of banned words
     */
    public function __construct()
    {
        $this->loadDictionary();
    }

    /**
     * Get the current dictionary for debugging
     * 
     * @return array
     */
    public function getDictionary()
    {
        return $this->dictionary;
    }

    /**
     * Load the dictionary of banned words
     *
     * @return void
     */
    protected function loadDictionary()
    {
        // You can load this from a file or config
        // For now, using a small sample list
        $this->dictionary = config('profanity.words', [
            // English profanity
            'ass', 'asshole', 'bastard', 'bitch', 'cunt', 'damn', 
            'fuck', 'fucking', 'shit', 'bullshit', 'piss', 'dick', 
            'cock', 'pussy', 'whore', 'slut', 'motherfucker', 
            'tits', 'crap', 'hell', 'idiot', 'stupid', 'dumb',
            
            // Tagalog profanity
            'putangina', 'puta', 'punyeta', 'gago', 'tangina', 
            'lintik', 'ulol', 'tarantado', 'hinayupak', 'inutil',
            'buwisit', 'kupal', 'tanga', 'bobo', 'pakyu', 'leche',
            'hayop', 'siraulo', 'ungas', 'tae', 'burat', 'pekpek',
            'pakshet', 'anak ng puta', 'iniyot', 'yawa', 'bilat'
        ]);
    }

    /**
     * Check if text contains profanity
     *
     * @param string $text The text to check
     * @return bool True if profanity found
     */
    public function hasProfanity($text)
    {
        if (empty($text)) {
            return false;
        }

        // Convert text to lowercase for case-insensitive matching
        $text = mb_strtolower($text);
        
        // Replace common character substitutions
        $text = $this->normalizeText($text);

        // Check for each banned word
        foreach ($this->dictionary as $word) {
            // Use word boundary to match whole words
            if (preg_match('/\b' . preg_quote($word, '/') . '\b/i', $text)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Replace common character substitutions used to bypass filters
     *
     * @param string $text The text to normalize
     * @return string Normalized text
     */
    protected function normalizeText($text)
    {
        $replacements = [
            '0' => 'o',
            '1' => 'i',
            '3' => 'e',
            '4' => 'a',
            '5' => 's',
            '@' => 'a',
            '$' => 's',
            '+' => 't',
            // Add more substitutions as needed
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }

    /**
     * Filter profanity from text by replacing with asterisks
     *
     * @param string $text The text to filter
     * @return string Filtered text
     */
    public function filter($text)
    {
        if (empty($text)) {
            return $text;
        }

        $filteredText = $text;
        
        foreach ($this->dictionary as $word) {
            $replacement = str_repeat('*', strlen($word));
            $filteredText = preg_replace('/\b' . preg_quote($word, '/') . '\b/i', $replacement, $filteredText);
        }

        return $filteredText;
    }
}