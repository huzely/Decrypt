const socket = io();
const board = document.getElementById('priceBoard');
const assetSelect = document.getElementById('assetSelect');

if (socket && board) {
  socket.on('prices', (prices) => {
    board.innerHTML = '';
    prices.forEach((p) => {
      const div = document.createElement('div');
      div.className = 'price-row';
      div.innerText = `${p.symbol}: ${p.price}`;
      board.appendChild(div);
    });
  });
}

if (assetSelect) {
  socket.on('prices', (prices) => {
    const symbol = assetSelect.options[assetSelect.selectedIndex]?.dataset.symbol;
    const current = prices.find((p) => p.symbol === symbol);
    if (current) {
      assetSelect.setAttribute('data-price', current.price);
    }
  });
}
