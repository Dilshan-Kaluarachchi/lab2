<?php

require_once 'Book.php';

class BookRepository {

    private string $filename;

    public function __construct(string $theFilename) {
        $this->filename = $theFilename;
    }

    public function getAllBooks(): array {
        if (!file_exists($this->filename)) {
            return [];
        }
        $fileContents = file_get_contents($this->filename);
        if (!$fileContents) {
            return [];
        }
        $decodedBooks = json_decode($fileContents, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }
        $books = [];
        foreach ($decodedBooks as $bookData) {
            $books[] = (new Book())->fill($bookData);
        }
        return $books;
    }

    public function saveBook(Book $book): void {
        $books = $this->getAllBooks();
        $books[] = $book;
        file_put_contents($this->filename, json_encode($books));
    }

    public function getBookByISBN(string $isbn): ?Book {
        $books = $this->getAllBooks();
        foreach ($books as $book) {
            if ($book->getInternationalStandardBookNumber() === $isbn) {
                return $book;
            }
        }
        return null;
    }

    public function getBookByTitle(string $title): ?Book {
        $books = $this->getAllBooks();
        foreach ($books as $book) {
            if ($book->getName() === $title) {
                return $book;
            }
        }
        return null;
    }

    public function updateBook(string $isbn, Book $newBook): void {
        $books = $this->getAllBooks();
        foreach ($books as $index => $book) {
            if ($book->getInternationalStandardBookNumber() === $isbn) {
                $books[$index] = $newBook;
            }
        }
        file_put_contents($this->filename, json_encode($books));
    }

    public function deleteBookByISBN(string $isbn): void {
        $books = $this->getAllBooks();
        foreach ($books as $index => $book) {
            if ($book->getInternationalStandardBookNumber() === $isbn) {
                array_splice($books, $index, 1);
            }
        }
        file_put_contents($this->filename, json_encode($books));
    }
}
