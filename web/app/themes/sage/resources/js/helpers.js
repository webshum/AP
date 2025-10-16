function getLang() {
    const langCode = navigator.language || 'uk';
    const lang = (langCode.startsWith('uk') || langCode.startsWith('ru'))
        ? 'uk'
        : 'pl';

    return lang;
}

export {
    getLang
}