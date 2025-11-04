<?php

namespace App\Helpers;

class LogoHelper
{
    /**
     * Get the URL for a specific logo variant
     *
     * @param string $variant
     * @return string
     */
    public static function url(string $variant = 'full'): string
    {
        $logoFiles = [
            'full' => 'siteledger-logo.svg',
            'sidebar' => 'siteledger-sidebar.svg',
            'icon' => 'siteledger-icon.svg',
            'favicon' => 'siteledger-favicon.ico'
        ];
        
        $file = $logoFiles[$variant] ?? $logoFiles['full'];
        
        return asset('images/logo/' . $file);
    }
    
    /**
     * Get all available logo variants
     *
     * @return array
     */
    public static function variants(): array
    {
        return [
            'full' => 'Full logo with text',
            'sidebar' => 'Compact sidebar version',
            'icon' => 'Icon only version',
            'favicon' => 'Favicon/browser tab icon'
        ];
    }
    
    /**
     * Get logo with proper HTML attributes
     *
     * @param string $variant
     * @param string $size
     * @param array $attributes
     * @return string
     */
    public static function img(string $variant = 'full', string $size = 'medium', array $attributes = []): string
    {
        $sizeClasses = [
            'small' => 'h-8',
            'medium' => 'h-12',
            'large' => 'h-16',
            'xl' => 'h-20'
        ];
        
        $url = self::url($variant);
        $sizeClass = $sizeClasses[$size] ?? $sizeClasses['medium'];
        
        $defaultAttributes = [
            'src' => $url,
            'alt' => 'SiteLedger Logo',
            'class' => $sizeClass . ' w-auto'
        ];
        
        $allAttributes = array_merge($defaultAttributes, $attributes);
        
        $attributeString = '';
        foreach ($allAttributes as $key => $value) {
            $attributeString .= $key . '="' . htmlspecialchars($value) . '" ';
        }
        
        return '<img ' . trim($attributeString) . '>';
    }
    
    /**
     * Get company information
     *
     * @return array
     */
    public static function companyInfo(): array
    {
        return [
            'name' => 'SiteLedger',
            'tagline' => 'Construction Finance Management',
            'description' => 'Comprehensive financial management for construction companies',
            'industry' => 'Construction Finance',
            'type' => 'Management System'
        ];
    }
}