<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Controllers;

use Psr\Http\Message\ResponseInterface;
use willitscale\Streetlamp\Attributes\Accepts;
use willitscale\Streetlamp\Attributes\Controller\RouteController;
use willitscale\Streetlamp\Attributes\Parameter\BodyParameter;
use willitscale\Streetlamp\Attributes\Parameter\FileParameter;
use willitscale\Streetlamp\Attributes\Parameter\HeaderParameter;
use willitscale\Streetlamp\Attributes\Parameter\PathParameter;
use willitscale\Streetlamp\Attributes\Parameter\PostParameter;
use willitscale\Streetlamp\Attributes\Parameter\QueryParameter;
use willitscale\Streetlamp\Attributes\Path;
use willitscale\Streetlamp\Attributes\Route\Method;
use willitscale\Streetlamp\Builders\ResponseBuilder;
use willitscale\Streetlamp\Enums\HttpMethod;
use willitscale\Streetlamp\Enums\HttpStatusCode;
use willitscale\Streetlamp\Enums\MediaType;
use willitscale\Streetlamp\Models\File;
use willitscale\Streetlamp\Models\ServerSentEvents\Data;
use willitscale\Streetlamp\Models\ServerSentEvents\Event;
use willitscale\Streetlamp\Models\ServerSentEvents\Id;
use willitscale\Streetlamp\Responses\ServerSentEventsDispatcher;

#[RouteController]
class MainController implements ServerSentEventsDispatcher
{
    #[Method(HttpMethod::GET)]
    #[Path('/query1')]
    public function query1(
        #[QueryParameter('name')] string $name = 'Unknown',
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData('Hello, ' . $name)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->setContentType(MediaType::TEXT_HTML)
            ->build();
    }

    #[Method(HttpMethod::GET)]
    #[Path('/query2')]
    public function query2(
        #[QueryParameter('name', true)] string $name = 'Unknown',
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData('Hello, ' . $name)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->setContentType(MediaType::TEXT_HTML)
            ->build();
    }

    #[Method(HttpMethod::GET)]
    #[Path('/path1/{name}')]
    public function path1(
        #[PathParameter('name', true)] string $name,
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData('Hello, ' . $name)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->setContentType(MediaType::TEXT_HTML)
            ->build();
    }

    #[Method(HttpMethod::POST)]
    #[Path('/post1')]
    public function post1(
        #[PostParameter('name', true)] string $name,
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData('Hello, ' . $name)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->setContentType(MediaType::TEXT_HTML)
            ->build();
    }

    #[Method(HttpMethod::POST)]
    #[Path('/post2')]
    public function post2(
        #[PostParameter('name', false)] string $name = 'Unknown',
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData('Hello, ' . $name)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->setContentType(MediaType::TEXT_HTML)
            ->build();
    }

    #[Method(HttpMethod::GET)]
    #[Path('/header1')]
    public function header1(
        #[HeaderParameter('name', true)] string $name,
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData('Hello, ' . $name)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->setContentType(MediaType::TEXT_HTML)
            ->build();
    }

    #[Method(HttpMethod::GET)]
    #[Path('/header2')]
    public function header2(
        #[HeaderParameter('name', false)] string $name = 'Unknown',
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData('Hello, ' . $name)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->setContentType(MediaType::TEXT_HTML)
            ->build();
    }

    #[Method(HttpMethod::POST)]
    #[Path('/body1')]
    #[Accepts(MediaType::APPLICATION_JSON)]
    public function body1(
        #[BodyParameter] string $name,
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData('Hello, ' . json_decode($name))
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->setContentType(MediaType::TEXT_HTML)
            ->build();
    }

    #[Method(HttpMethod::GET)]
    #[Path('/file_upload')]
    public function fileUpload(): ResponseInterface
    {
        return new ResponseBuilder()
            ->setData(
                '<form action="/file1" method="post" enctype="multipart/form-data">
  <div>
    <label for="file">Choose file to upload</label>
    <input type="file" id="file" name="file" multiple />
  </div>
  <div>
    <button>Submit</button>
  </div>
</form>'
            )
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->setContentType(MediaType::TEXT_HTML)
            ->build();
    }

    #[Method(HttpMethod::POST)]
    #[Path('/file1')]
    public function file1(
        #[FileParameter('file')] File $file,
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData($file)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->build();
    }

    #[Method(HttpMethod::POST)]
    #[Path('/data1')]
    #[Accepts(MediaType::APPLICATION_JSON)]
    public function data1(
        #[BodyParameter] TestModel $data,
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData($data)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->build();
    }


    #[Method(HttpMethod::GET)]
    #[Path('/sse-demo')]
    public function sseDemo(): ResponseInterface
    {
        return new ResponseBuilder()
            ->setData(file_get_contents(__DIR__ . '/sse-demo.html'))
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->setContentType(MediaType::TEXT_HTML)
            ->build();
    }

    #[Method(HttpMethod::GET)]
    #[Path('/sse')]
    public function sse(): ResponseInterface
    {
        return new ResponseBuilder()
            ->setStreamDispatcher($this)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->setContentType(MediaType::TEXT_EVENT_STREAM)
            ->build();
    }

    public function dispatch(): array
    {
        return [
            new Id('123'),
            new Event('ping'),
            new Data(new TestModel("Test", "test@example.com", new AgeModel(12)))
        ];
    }

    public function isRunning(): bool
    {
        return true;
    }

    public function delay(): void
    {
        sleep(1);
    }
}
