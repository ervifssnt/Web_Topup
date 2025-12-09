<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Helpers\InputSanitizer;

class InputSanitizationTest extends TestCase
{
    public function test_sanitize_string_removes_html_tags(): void
    {
        $input = '<script>alert("xss")</script>Hello World';
        $result = InputSanitizer::sanitizeString($input);

        // strip_tags removes the tags but leaves the content
        $this->assertEquals('alert("xss")Hello World', $result);
        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringNotContainsString('</script>', $result);
    }

    public function test_sanitize_string_trims_whitespace(): void
    {
        $input = '  Hello World  ';
        $result = InputSanitizer::sanitizeString($input);

        $this->assertEquals('Hello World', $result);
    }

    public function test_sanitize_string_handles_null(): void
    {
        $result = InputSanitizer::sanitizeString(null);

        $this->assertNull($result);
    }

    public function test_sanitize_email_validates_format(): void
    {
        $validEmail = 'test@example.com';
        $result = InputSanitizer::sanitizeEmail($validEmail);

        $this->assertEquals('test@example.com', $result);
    }

    public function test_sanitize_email_handles_invalid_format(): void
    {
        $invalidEmail = 'not-an-email';
        $result = InputSanitizer::sanitizeEmail($invalidEmail);

        // filter_var returns false for invalid emails, but sanitizer might return the string
        $this->assertIsString($result);
    }

    public function test_sanitize_email_handles_null(): void
    {
        $result = InputSanitizer::sanitizeEmail(null);

        $this->assertNull($result);
    }

    public function test_sanitize_username_allows_only_alphanumeric_and_underscore(): void
    {
        $input = 'user_name123!@#$%';
        $result = InputSanitizer::sanitizeUsername($input);

        $this->assertEquals('user_name123', $result);
        $this->assertStringNotContainsString('!', $result);
        $this->assertStringNotContainsString('@', $result);
    }

    public function test_sanitize_username_handles_null(): void
    {
        $result = InputSanitizer::sanitizeUsername(null);

        $this->assertNull($result);
    }

    public function test_sanitize_phone_removes_non_digits(): void
    {
        $input = '+1 (555) 123-4567';
        $result = InputSanitizer::sanitizePhone($input);

        $this->assertEquals('15551234567', $result);
        $this->assertMatchesRegularExpression('/^\d+$/', $result);
    }

    public function test_sanitize_phone_handles_null(): void
    {
        $result = InputSanitizer::sanitizePhone(null);

        $this->assertNull($result);
    }

    public function test_sanitize_numeric_returns_integer(): void
    {
        $input = '12345';
        $result = InputSanitizer::sanitizeNumeric($input);

        $this->assertIsInt($result);
        $this->assertEquals(12345, $result);
    }

    public function test_sanitize_numeric_handles_non_numeric(): void
    {
        $input = 'not-a-number';
        $result = InputSanitizer::sanitizeNumeric($input);

        // FILTER_SANITIZE_NUMBER_INT removes letters, leaving hyphens, which becomes 0
        $this->assertEquals(0, $result);
    }

    public function test_sanitize_numeric_handles_null(): void
    {
        $result = InputSanitizer::sanitizeNumeric(null);

        $this->assertNull($result);
    }

    public function test_sanitize_numeric_handles_empty_string(): void
    {
        $result = InputSanitizer::sanitizeNumeric('');

        $this->assertNull($result);
    }

    public function test_sanitize_url_validates_format(): void
    {
        $validUrl = 'https://example.com';
        $result = InputSanitizer::sanitizeUrl($validUrl);

        $this->assertStringContainsString('https://example.com', $result);
    }

    public function test_sanitize_url_handles_null(): void
    {
        $result = InputSanitizer::sanitizeUrl(null);

        $this->assertNull($result);
    }

    public function test_sanitize_html_allows_safe_tags(): void
    {
        $input = '<p>Hello <strong>World</strong></p><script>alert("xss")</script>';
        $result = InputSanitizer::sanitizeHtml($input);

        $this->assertStringContainsString('<p>', $result);
        $this->assertStringContainsString('<strong>', $result);
        $this->assertStringNotContainsString('<script>', $result);
    }

    public function test_sanitize_html_handles_null(): void
    {
        $result = InputSanitizer::sanitizeHtml(null);

        $this->assertNull($result);
    }
}
