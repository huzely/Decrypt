const EventEmitter = require('events');
const { Asset } = require('../models');

class PriceFeed extends EventEmitter {
  constructor() {
    super();
    this.prices = {};
  }

  async init() {
    const assets = await Asset.findAll();
    assets.forEach((asset) => {
      this.prices[asset.symbol] = this.prices[asset.symbol] || this.randomStart();
    });
    setInterval(() => this.tick(), 1000);
  }

  randomStart() {
    return 1 + Math.random() * 100;
  }

  tick() {
    Object.keys(this.prices).forEach((symbol) => {
      const delta = (Math.random() - 0.5) * 0.2;
      this.prices[symbol] = Math.max(0.0001, this.prices[symbol] + delta);
      this.emit('price', { symbol, price: this.prices[symbol] });
    });
  }

  getPrice(symbol) {
    const price = this.prices[symbol];
    if (!price) {
      this.prices[symbol] = this.randomStart();
      return this.prices[symbol];
    }
    return price;
  }
}

module.exports = new PriceFeed();
