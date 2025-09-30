/**
 * @module n2words
 */
alert(11111);
/**
 * Creates new common language class that processes decimals separately.
 * Requires implementing `toCardinal`.
 */
class AbstractLanguage {
  #negativeWord;
  #separatorWord;
  #zero;
  #spaceSeparator;
  #wholeNumber;

  /**
   * @param {object} options Options for class.
   * @param {string} [options.negativeWord] Word that precedes a negative number (if any).
   * @param {string} options.separatorWord Word that separates cardinal numbers (i.e. "and").
   * @param {string} options.zero Word for 0 (i.e. "zero").
   * @param {string} [options.spaceSeparator] Character that separates words.
   */
  constructor(options) {
    // Merge supplied options with defaults
    options = Object.assign({
      negativeWord: '',
      separatorWord: '',
      zero: '',
      spaceSeparator: ' '
    }, options);

    // Make options available to class
    this.#negativeWord = options.negativeWord;
    this.#separatorWord = options.separatorWord;
    this.#zero = options.zero;
    this.#spaceSeparator = options.spaceSeparator;
  }

  /**
   * @returns {string} Word that precedes a negative number (if any).
   */
  get negativeWord() {
    return this.#negativeWord;
  }

  /**
   * @returns {string} Word that separates cardinal numbers (i.e. "and").
   */
  get separatorWord() {
    return this.#separatorWord;
  }

  /**
   * @returns {string} Word for 0 (i.e. "zero").
   */
  get zero() {
    return this.#zero;
  }

  /**
   * @returns {string} Character that separates words.
   */
  get spaceSeparator() {
    return this.#spaceSeparator;
  }

  /**
   * @returns {number} Input value without decimal.
   */
  get wholeNumber() {
    return this.#wholeNumber;
  }

  /**
   * Convert ONLY decimal portion of number (processing leading zeros) to a string array of cardinal numbers.
   * @param {string} decimal Decimal string to convert.
   * @returns {string} Value in written format.
   */
  decimalToCardinal(decimal) {
    const words = [];

    // Split decimal string into an array of characters
    const chars = [...decimal];

    // Loop through characters adding leading zeros to words array
    let index = 0;
    while (index < chars.length && chars[index] === '0') {
      words.push(this.zero);
      index++;
    }

    // Prevent further processing if entire string was zeros
    if (index === chars.length) {
      return words;
    }

    // Convert and add remaining then return words array
    return [...words, this.toCardinal(BigInt(decimal))];
  }

  /**
   * Convert a number to cardinal form.
   * @param {number|string|bigint} value Number to be convert.
   * @returns {string} Value in written format.
   * @throws {Error} Value must be a valid number.
   */
  floatToCardinal(value) {
    // Validate user input value and convert to string (excluding BigInt)
    if (typeof value == 'number') {
      if (Number.isNaN(value)) {
        throw new TypeError('NaN is not an accepted number.');
      }
      value = value.toString();
    } else if (typeof value == 'string') {
      value = value.trim();
      if (value.length === 0 || Number.isNaN(Number(value))) {
        throw new Error('"' + value + '" is not a valid number.');
      }
    } else if (typeof value != 'bigint') {
      throw new TypeError('Invalid variable type: ' + typeof value);
    }

    let words = [];
    let wholeNumber;
    let decimalNumber;

    // If negative number add negative word
    if (value < 0) {
      words.push(this.negativeWord);
    }

    // Split value decimal (if any) then convert to BigInt
    if (typeof value == 'bigint') {
      wholeNumber = value;
    } else {
      const splitValue = value.split('.');
      wholeNumber = BigInt(splitValue[0]);
      decimalNumber = splitValue[1];
    }

    // Convert whole number to positive (if negative)
    if (wholeNumber < 0) {
      wholeNumber = -wholeNumber;
    }

    // NOTE: Only needed for CZ
    this.#wholeNumber = wholeNumber;

    // Add whole number in written form
    words = [...words, this.toCardinal(wholeNumber)];

    // Add decimal number in written form (if any)
    if (decimalNumber) {
      words.push(this.separatorWord);

      words = [...words, ...this.decimalToCardinal(decimalNumber)];
    }

    // Join words with spaces
    return words.join(this.spaceSeparator);
  }
}

export default AbstractLanguage;


/**
 * Creates new common language class that uses a highest matching word value algorithm.
 * Number matching word {@link cards} must be provided for this to work.
 * See {@link AbstractLanguage} for further requirements.
 */
class BaseLanguage extends AbstractLanguage {
  #cards;

  /**
   * @param {object} options Options for class.
   * @param {string} [options.negativeWord] Word that precedes a negative number (if any).
   * @param {string} options.separatorWord Word that separates cardinal numbers (i.e. "and").
   * @param {string} options.zero Word for 0 (i.e. "zero").
   * @param {string} [options.spaceSeparator] Character that separates words.
   * @param {Array} cards Array of number matching "cards" from highest-to-lowest.
   */
  constructor(options, cards) {
    super(options);

    this.#cards = cards;
  }

  /**
   * Array of number matching "cards" from highest-to-lowest.
   * First element in card array is the number to match while the second is the word to use.
   * @example
   * [
   *   ...
   *   [100, 'hundred'],
   *   ...
   *   [1, 'one'],
   * ]
   * @type {Array}
   */
  get cards() {
    return this.#cards;
  }

  set cards(value) {
    this.#cards = value;
  }

  /**
   * Get word for number if it matches a language card.
   * @param {number|bigint} number Card number value.
   * @returns {string|undefined} Return card word or undefined if no card.
   */
  getCardWord(number) {
    // Get matching card from number
    const card = this.cards.find(_card => _card[0] == number);

    // Return card word or undefined if no card found
    return (Array.isArray(card) ? card[1] : undefined);
  }

  /**
   * Get array of card matches.
   * @param {number|bigint} value The number value to convert to cardinal form.
   * @returns {object} Word sets (and pairs) from value.
   */
  // TODO Simplify return object.
  toCardMatches(value) {
    const out = [];
    let remaining = value;

    do {
      // Find card with highest matching number
      const card = this.cards.find(card => {
        return remaining >= card[0];
      });

      let quantity; // Quantity of card set values

      // Calculate quantity and remaining value
      // Override variables for 0 as math will fail
      if (remaining == 0) {
        quantity = 1;
        remaining = 0;
      } else {
        quantity = remaining / card[0];
        remaining = remaining % card[0];
      }

      // Is value perfect match of card number?
      if (quantity == 1) {
        // TODO Merge word set pairs together (if possible) to simplify return object
        out.push({
          [this.getCardWord(1)]: 1,
        });
      } else {
        // TODO Understand the logic for this
        /*if (quantity == remaining) {
          return [(quantity * this.getCardWord(card[0]), quantity * card[0])];
        }*/

        // TODO Remove reciprocating calls.
        out.push(this.toCardMatches(quantity));
      }

      // Add matching word set to output list
      out.push({
        [card[1]]: card[0],
      });
    }
    while (remaining > 0);

    return out;
  }

  clean(words) {
    let out = words;

    // Loop through word sets while array size is greater or less than 1
    // TODO Change logic to work in for loop to better understand loop intentions
    while (words.length != 1) {
      out = [];
      const left = words[0];
      const right = words[1];

      // Are the first & second word sets arrays?
      if (!Array.isArray(left) && !Array.isArray(right)) {
        // Merge word set pair and add to output array
        out.push(this.merge(left, right));

        // TODO Understand
        if (words.slice(2).length > 0) {
          out.push(words.slice(2));
        }
      } else {
        // Loop through
        for (const element of words) {

          if (Array.isArray(element)) {
            if (element.length == 1) out.push(element[0]);
            else out.push(this.clean(element));
          } else {
            out.push(element);
          }
        }
      }

      words = out;
    }

    return out[0];
  }

  postClean(out0) {
    return out0.trimEnd();
  }

  /**
   * Convert a whole number to written format.
   * @param {number|bigint} value The number value to convert to cardinal form.
   * @returns {string} Value in written format.
   */
  toCardinal(value) {
    // Convert value to word sets
    const words = this.toCardMatches(value);

    // Process word sets
    const preWords = Object.keys(this.clean(words))[0];

    // Process word sets some more and return result
    // TODO Look into language functions/events
    return this.postClean(preWords);
  }
}

export default BaseLanguage;



export class N2WordsFR extends BaseLanguage {
  constructor(options) {
    options = Object.assign({
      negativeWord: 'moins',
      separatorWord: 'virgule',
      zero: 'zéro',
      _region: 'FR'
    }, options);

    super(options, [
      [1_000_000_000_000_000_000_000_000_000n, 'quadrilliard'],
      [1_000_000_000_000_000_000_000_000n, 'quadrillion'],
      [1_000_000_000_000_000_000_000n, 'trilliard'],
      [1_000_000_000_000_000_000n, 'trillion'],
      [1_000_000_000_000_000n, 'billiard'],
      [1_000_000_000_000n, 'billion'],
      [1_000_000_000n, 'milliard'],
      [1_000_000n, 'million'],
      [1000n, 'mille'],
      [100n, 'cent'],
      ...(['BE'].includes(options._region) ? [[90n, 'nonante']] : []),
      [80n, 'quatre-vingts'],
      ...(['BE'].includes(options._region) ? [[70n, 'septante']] : []),
      [60n, 'soixante'],
      [50n, 'cinquante'],
      [40n, 'quarante'],
      [30n, 'trente'],
      [20n, 'vingt'],
      [19n, 'dix-neuf'],
      [18n, 'dix-huit'],
      [17n, 'dix-sept'],
      [16n, 'seize'],
      [15n, 'quinze'],
      [14n, 'quatorze'],
      [13n, 'treize'],
      [12n, 'douze'],
      [11n, 'onze'],
      [10n, 'dix'],
      [9n, 'neuf'],
      [8n, 'huit'],
      [7n, 'sept'],
      [6n, 'six'],
      [5n, 'cinq'],
      [4n, 'quatre'],
      [3n, 'trois'],
      [2n, 'deux'],
      [1n, 'un'],
      [0n, 'zéro']
    ]);
  }

  merge(current, next) { // {'cent':100}, {'vingt-cinq':25}
    let cText = Object.keys(current)[0];
    let nText = Object.keys(next)[0];
    const cNumber = BigInt(Object.values(current)[0]);
    const nNumber = BigInt(Object.values(next)[0]);
    if (cNumber == 1) {
      if (nNumber < 1_000_000) {
        return { [nText]: nNumber };
      }
    } else {
      if (
        ((cNumber - 80n) % 100n == 0 || (cNumber % 100n == 0 && cNumber < 1000)) &&
        nNumber < 1_000_000 &&
        cText.at(-1) == 's'
      ) {
        cText = cText.slice(0, -1); // without last elem
      }
      if (
        cNumber < 1000 && nNumber != 1000 &&
        nText.at(-1) != 's' &&
        nNumber % 100n == 0
      ) {
        nText += 's';
      }
    }
    if (nNumber < cNumber && cNumber < 100) {
      if (nNumber % 10n == 1 && cNumber != 80) return { [`${cText} et ${nText}`]: cNumber + nNumber };
      return { [`${cText}-${nText}`]: cNumber + nNumber };
    }
    if (nNumber > cNumber) return { [`${cText} ${nText}`]: cNumber * nNumber };
    return { [`${cText} ${nText}`]: cNumber + nNumber };
  }
}

/**
 * Converts a value to cardinal (written) form.
 * @param {number|string|bigint} value Number to be convert.
 * @param {object} [options] Options for class.
 * @returns {string} Value in cardinal (written) format.
 * @throws {Error} Value cannot be invalid.
 */
export default function floatToCardinal (value, options = {}) {
  return new N2WordsFR(options).floatToCardinal(value);
}

const dict = {
  'ar': n2wordsAR,
  'az': n2wordsAZ,
  'cz': n2wordsCZ,
  'de': n2wordsDE,
  'dk': n2wordsDK,
  'en': n2wordsEN,       // default
  'es': n2wordsES,
  'fa': n2wordsFA,
  'fr': n2wordsFR,
  'fr-BE': n2wordsFRBE,
  'he': n2wordsHE,       // currently only for numbers < 10000
  'hr': n2wordsHR,
  'hu': n2wordsHU,
  'id': n2wordsID,
  'it': n2wordsIT,
  'ko': n2wordsKO,
  'lt': n2wordsLT,
  'lv': n2wordsLV,
  'nl': n2wordsNL,
  'no': n2wordsNO,
  'pl': n2wordsPL,
  'pt': n2wordsPT,
  'ru': n2wordsRU,
  'sr': n2wordsSR,
  'tr': n2wordsTR,
  'uk': n2wordsUK,
  'vi': n2wordsVI,
  'zh': n2wordsZH,
};

/**
 * Converts a number to written form.
 * @param {number|string|bigint} value The number to convert.
 * @param {object} [options] User options.
 * @returns {string} Value in written format.
 * TODO [2024-06] Migrate to object destructing for parameters
 */
// eslint-disable-next-line unicorn/no-object-as-default-parameter
function floatToCardinal(value, options = { lang: 'en' }) {
  const function_ = dict[options.lang];
  if (function_ != undefined) return function_(value, options);

  const fallbackLang = options.lang.split('-') // 'en-UK' -> ['en', 'UK']
    .map((_, index, array) => array.slice(0, array.length - index).join('-')) // ['en-UK', 'en']
    .find(l => dict[l] != undefined);
  if (fallbackLang != undefined) return dict[fallbackLang](value, options);

  throw new Error('Unsupported language: ' + value + '.');
}

export default floatToCardinal;
