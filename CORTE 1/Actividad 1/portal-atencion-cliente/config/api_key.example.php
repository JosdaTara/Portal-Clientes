<?php
/**
 * Configuracion de la API de Google Gemini
 * 
 * INSTRUCCIONES:
 * 1. Crear cuenta en https://aistudio.google.com
 * 2. Ir a https://aistudio.google.com/apikey
 * 3. Crear una nueva API key (GRATIS)
 * 4. Pegarla entre comillas en GEMINI_API_KEY
 * 
 * Tier gratuito: 15 RPM, 1M tokens/dia - MAS que suficiente para este portal.
 * Si no tienes API key, el sistema funcionara con analisis local (fallback).
 */

define('GEMINI_API_KEY', 'TU_API_KEY_AQUI');

// Configuracion del modelo
define('GEMINI_MODEL', 'gemini-3.6-flash');
define('GEMINI_MAX_TOKENS', 2048);
