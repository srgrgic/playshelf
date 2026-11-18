const polje = document.getElementById('pretraga');
const rezultati = document.getElementById('rezultati');
let tajmer;

polje.addEventListener('input', () => {
  clearTimeout(tajmer);
  tajmer = setTimeout(() => pretrazi(polje.value.trim()), 400);
});

async function pretrazi(upit) {
  if (upit.length < 2) {
    rezultati.innerHTML = '';
    return;
  }
  rezultati.innerHTML = '<p>Učitavanje...</p>';
  try {
    const odgovor = await fetch(`/api/v1/igre?q=${encodeURIComponent(upit)}`);
    const json = await odgovor.json();
    if (json.greska) {
      rezultati.innerHTML = '<p>Servis trenutno nije dostupan.</p>';
      return;
    }
    if (json.podaci.igre.length === 0) {
      rezultati.innerHTML = '<p>Nema rezultata.</p>';
      return;
    }
    rezultati.innerHTML = json.podaci.igre.map(igra => `
      <article class="kartica">
        <img src="${igra.slika ?? ''}" alt="${igra.naslov}" loading="lazy">
        <h3>${igra.naslov}</h3>
        <p>Ocjena: ${igra.ocjena ?? '-'}</p>
      </article>
    `).join('');
  } catch (e) {
    rezultati.innerHTML = '<p>Greška pri dohvatu podataka.</p>';
  }
}