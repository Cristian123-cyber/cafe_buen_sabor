const phrasesByRole = {
  5: [
    "Un buen líder no crea seguidores, crea más líderes.",
    "La estrategia sin ejecución es solo una ilusión.",
    "Controla el sistema, no dejes que el sistema te controle.",
    "Cada decisión hoy es una mejora mañana.",
    "Gestión efectiva, éxito asegurado."
  ],
  3: [
    "Cada cliente atendido es una oportunidad de fidelizar.",
    "Tu atención marca la diferencia.",
    "Eficiencia y sonrisa, la fórmula perfecta.",
    "El control en caja, la tranquilidad del negocio.",
    "Cada cierre es una historia bien contada."
  ],
  1: [
    "Una buena atención se saborea más que el plato.",
    "Servir con el corazón deja propina en el alma.",
    "Tu actitud es el mejor ingrediente del servicio.",
    "Un cliente feliz siempre regresa.",
    "Detalles pequeños crean experiencias memorables."
  ],
  2: [
    "Cocina con pasión, no con presión.",
    "Cada plato es una obra de arte comestible.",
    "Tus manos crean experiencias inolvidables.",
    "La cocina es el corazón del restaurante.",
    "Un buen sabor se recuerda, un gran plato se recomienda."
  ]
};

export function getRandomPhraseByRole(role = 5) {
  
  const phrases = phrasesByRole[role] || phrasesByRole[5];
  const randomIndex = Math.floor(Math.random() * phrases.length);
  return phrases[randomIndex];
}
