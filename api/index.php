<?php

/**
 * Vercel entrypoint.
 *
 * A Vercel não executa PHP nativamente: o runtime comunitário `vercel-php`
 * sobe um servidor PHP embutido e usa este ficheiro como router.
 * Delegamos tudo para o front controller da aplicação em `public/index.php`,
 * que resolve as rotas com base no REQUEST_URI original.
 */

require __DIR__ . '/../public/index.php';
