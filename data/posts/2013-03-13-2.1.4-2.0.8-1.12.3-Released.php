<?php
use PhlyBlog\AuthorEntity;
use PhlyBlog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail('matthew@zend.com');
$author->setUrl('http://mwop.net/');

$post = new EntryEntity();
$post->setId('2013-03-14-zend-framework-3-for-1-release-day');
$post->setTitle('Zend Framework 2.1.4, 2.0.8, and 1.12.3 Released!');
$post->setAuthor($author);
$post->setDraft(false);
$post->setPublic(true);
$post->setCreated(new DateTime('2013-03-14 10:30', new DateTimezone('America/Chicago')));
$post->setUpdated(new DateTime('2013-03-14 10:30', new DateTimezone('America/Chicago')));
$body =<<<'EOS'
<p>
    The Zend Framework community is pleased to announce the immediate availability
    of three new releases: 2.1.4, 2.0.8, and 1.12.3!  Packages and installation 
    instructions are available at:
</p>

<ul>
    <li>
        <a href="/downloads/latest">http://framework.zend.com/downloads/latest</a>
    </li>
</ul>

<p>
    The ZF2 releases include three security updates, and all ZF versions also 
    include updates to the Twitter component to follow the Twitter v1.1 API, 
    which is not backwards compatible with previous versions.
</p>

EOS;
$post->setBody($body);

$extended =<<<'EOC'

<h2>Security Fixes</h2>

<p>
    2.1.4 and 2.0.8 contain three security fixes.
</p>

<h3>Query Route</h3>

<p>
    We were alerted to the fact that the Query route could override parameters
    matched in parent routes, effectively bypassing constraints defined. In
    particular, this could result in overriding the controller or action matched
    by a given route.
</p>

<p>
    The query route was deprecated, as a replacement exists within the HTTP router
    itself. You can pass a "query" option to the assemble method containing either
    the query string or an array of key-value pairs:
</p>

<pre class="highlight">
$url = $router->assemble(array(
    'name' => 'foo',
), array(
    'query' => array(
        'page' => 3,
        'sort' => 'DESC',
    ), 
    // or: 'query' => 'page=3&sort=DESC'
));

// via URL helper/plugin:
$rendererOrController->url('foo', array(), array('query' => $request->getQuery()));
</pre>

<p>
    Additionally, the merging of query parameters into the route match was removed
    entirely. Please use the query container of the request object instead.
</p>

<p>
    For more information on the security vector, please see
    <a href="http://framework.zend.com/security/advisory/ZF2013-01">ZF2013-01</a>.
</p>

<h3>Random Number Generation</h3>

<p>
    The <code>Zend\Math\Rand</code> component generates random bytes using the OpenSSL
    or Mcrypt extensions when available but will otherwise use PHP's
    <code>mt_rand()</code> function as a fallback. All outputs from <code>mt_rand()</code> are
    predictable for the same PHP process if an attacker can brute force
    the seed - which can be done if the attacker has access to a random number
    generated by `mt_rand` or the session ID (if generated without using additional
    entropy). 
</p>

<p>
    Zend Framework have revised the <code>Zend\Math\Rand</code> component to replace the
    current <code>mt_rand()</code> fallback for OpenSSL/Mcrypt with Anthony Ferrara's
    <a href="https://github.com/ircmaxell/RandomLib">RandomLib</a>, incorporating an additional
    entropy source based on <a href="https://github.com/GeorgeArgyros/Secure-random-bytes-in-PHP">source code published by George Argyros</a>. The new
    fallback collects entropy from numerous sources other than PHP's internal seed
    mechanism and extracts random bytes from the resulting mixed entropy pool.
</p>

<p>
    For more information on this security vector, please see
    <a href="http://framework.zend.com/security/advisory/ZF2013-02">ZF2013-02</a>.
</p>

<h3>Database Platform Quoting</h3>

<p>
    Altered <code>Zend\Db</code> to throw notices when insecure usage of the 
    following methods is called: 
</p>

<ul>
    <li><code>Zend\Db\Adapter\Platform\*::quoteValue*()</code></li>
    <li><code>Zend\Db\Sql\*::getSqlString*()</code></li>
</ul>

<p>
    Fixed <code>Zend\Db</code> Platform objects to use driver level quoting when provided, and
    throw <code>E_USER_NOTICE</code> when not provided.  Added <code>quoteTrustedValue()</code> API for
    notice-free value quoting.  Fixed all userland quoting in Platform objects to
    handle a wider array of escapable characters.
</p>

<p>
    For more information on this security vector, please see
    <a href="http://framework.zend.com/security/advisory/ZF2013-03">ZF2013-03</a>.
</p>

<h2>Twitter API Updates</h2>

<p>
    Twitter has begun sunsetting its v1.0 API, and has introduced rolling 
    blackouts in order to prompt developers to move to the v1.1 API. 
    Unfortunately, v1.1 is not backwards compatible with v1.0, so a number
    of backwards-breaking changes need to be made.
</p>

<p>
    Version 2.1.0 of <a href="https://github.com/zendframework/ZendService_Twitter">ZendService_Twitter</a>
    and version 1.12.3 of Zend Framework have been released with support for 
    v1.1 of the Twitter API. A number of service endpoints were removed, and others
    moved to new namespaces. As such, if you use the component, you are urged to upgrade,
    and we encourage you to read the documentation to see what methods are now available,
    and how to use OAuth access tokens with the service.
</p>

<h2>Polyfill Support Fixes</h2>

<p>
    Polyfills (version-specific class replacements) have caused some issues in 
    the 2.1 series for users of <code>Zend\Stdlib</code> and 
    <code>Zend\Session</code>.  In particular, users who were not using Composer 
    were unaware/uncertain about what extra files needed to be included to load 
    polyfills, and those users who were generating classmaps were running into 
    issues since the same class was being generated twice.
</p>

<p>
    New polyfill support was created which does the following:
</p>

<ul>
    <li>New, uniquely named classes were created for each polyfill base.</li>

    <li>A stub class file was created for each class needing polyfill support. 
    A conditional is present in each that uses <code>class_alias</code> to 
    alias the appropriate polyfill base as an import. The stub class then 
    extends the base.</li>

    <li>The <code>compatibility/autoload.php</code> files in each component affected was altered to trigger an <code>E_USER_DEPRECATED</code> error asking the user to remove the require statement for the file.</li>
</ul>

<p>
    The functionality works with both Composer and ZF2's autoloading support, using
    either PSR-0 or classmaps. All typehinting is preserved.
</p>

<h2>Changelog</h2>

<p>
    Below are links to the changelogs for each version. (2.0.8 does not have a 
    separate changelog as it incorporates fixes backported from 2.1.4.)
</p>

<ul>
    <li><a href="/changelog/2.1.4">2.1.4 Changelog</a></li>
    <li><a href="/changelog/1.12.3">1.12.3 Changelog</a></li>
</ul>

<h2>Thank You!</h2>

<p>
    I'd like to thank our main contributors to this release. In particular, 
    Pádraic Brady and Enrico Zimuel for researching and implementing the Random
    Number Generator vulnerability and fixes; Ben Scholzen for implemting fixes
    for the Query route; Ralph Schindler, for his fixes for the database platform
    quoting vulnerabilities; and Mike Willbanks, for continuing to work on 
    solutions for session storage and timing issues.
</p>

<h2>Roadmap</h2>

<p>
    Maintenance releases happen monthly on the third Wednesday; expect version 2.1.5
    to drop 17 April 2013. We're also gearing up for version 2.2.0, which we are 
    targetting at the end of April 2013/early May.
</p>

EOC;
$post->setExtended($extended);

return $post;

