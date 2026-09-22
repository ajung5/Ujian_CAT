#!/usr/bin/env python3

from __future__ import annotations

import argparse
import re
import sys
from pathlib import Path


VIEWS_PATH = Path("resources/views")

PROTECTED_TAGS = {
    "pre",
    "textarea",
    "script",
    "style",
}

VOID_TAGS = {
    "area",
    "base",
    "br",
    "col",
    "embed",
    "hr",
    "img",
    "input",
    "link",
    "meta",
    "param",
    "source",
    "track",
    "wbr",
}


def tag_name_from_start(line: str) -> str | None:
    match = re.match(
        r"^\s*<\s*([a-zA-Z][a-zA-Z0-9:-]*)\b",
        line,
    )

    if not match:
        return None

    return match.group(1).lower()


def closing_tag_name(line: str) -> str | None:
    match = re.match(
        r"^\s*</\s*([a-zA-Z][a-zA-Z0-9:-]*)\s*>\s*$",
        line,
    )

    if not match:
        return None

    return match.group(1).lower()


def protected_line_mask(lines: list[str]) -> list[bool]:
    mask = [False] * len(lines)
    active_tag: str | None = None

    for index, line in enumerate(lines):
        stripped = line.strip()

        if active_tag is not None:
            mask[index] = True

            if re.search(
                rf"</\s*{re.escape(active_tag)}\s*>",
                stripped,
                re.IGNORECASE,
            ):
                active_tag = None

            continue

        tag = tag_name_from_start(line)

        if tag not in PROTECTED_TAGS:
            continue

        mask[index] = True

        if re.search(
            rf"</\s*{re.escape(tag)}\s*>",
            stripped,
            re.IGNORECASE,
        ):
            continue

        active_tag = tag

    return mask


def opening_container_tag_ending_at(
    lines: list[str],
    end_index: int,
) -> str | None:
    stripped_end = lines[end_index].strip()

    if ">" not in stripped_end:
        return None

    start_index = end_index
    lower_bound = max(0, end_index - 30)

    while start_index >= lower_bound:
        stripped = lines[start_index].lstrip()

        if stripped.startswith("<"):
            break

        start_index -= 1

    if start_index < lower_bound:
        return None

    segment = "\n".join(
        lines[start_index:end_index + 1]
    ).strip()

    if (
        not segment.startswith("<")
        or segment.startswith("</")
        or segment.startswith("<!--")
        or segment.startswith("<!")
        or segment.startswith("<?")
    ):
        return None

    match = re.match(
        r"<\s*([a-zA-Z][a-zA-Z0-9:-]*)\b",
        segment,
    )

    if not match:
        return None

    tag = match.group(1).lower()

    if tag in PROTECTED_TAGS:
        return None

    if tag == "html":
        return None

    if tag in VOID_TAGS:
        return None

    if segment.rstrip().endswith("/>"):
        return None

    if re.search(
        rf"</\s*{re.escape(tag)}\s*>",
        segment,
        re.IGNORECASE,
    ):
        return None

    return tag


def cleanup_text(text: str) -> str:
    had_final_newline = text.endswith("\n")
    lines = text.splitlines()

    if not lines:
        return text

    protected = protected_line_mask(lines)
    result: list[str] = []
    index = 0

    while index < len(lines):
        line = lines[index]

        if line.strip() != "":
            result.append(line)
            index += 1
            continue

        if protected[index]:
            result.append(line)
            index += 1
            continue

        previous_index = index - 1

        while (
            previous_index >= 0
            and lines[previous_index].strip() == ""
        ):
            previous_index -= 1

        next_index = index + 1

        while (
            next_index < len(lines)
            and lines[next_index].strip() == ""
        ):
            next_index += 1

        remove_blank = False

        if (
            previous_index >= 0
            and not protected[previous_index]
            and opening_container_tag_ending_at(
                lines,
                previous_index,
            )
            is not None
        ):
            remove_blank = True

        if (
            next_index < len(lines)
            and not protected[next_index]
        ):
            closing_tag = closing_tag_name(
                lines[next_index]
            )

            if (
                closing_tag is not None
                and closing_tag != "html"
                and closing_tag not in PROTECTED_TAGS
            ):
                remove_blank = True

        if remove_blank:
            index += 1
            continue

        result.append(line)
        index += 1

    final = "\n".join(result)

    if had_final_newline:
        final += "\n"

    return final


def blade_files() -> list[Path]:
    if not VIEWS_PATH.exists():
        return []

    return sorted(
        VIEWS_PATH.rglob("*.blade.php")
    )


def run_write(files: list[Path]) -> int:
    updated = 0

    for path in files:
        original = path.read_text(
            encoding="utf-8"
        )

        cleaned = cleanup_text(original)

        if cleaned == original:
            continue

        path.write_text(
            cleaned,
            encoding="utf-8",
        )

        updated += 1
        print(f"UPDATED: {path}")

    print()
    print(f"Selesai. {updated} file diperbarui.")

    return 0


def run_check(files: list[Path]) -> int:
    invalid: list[Path] = []

    for path in files:
        original = path.read_text(
            encoding="utf-8"
        )

        if cleanup_text(original) != original:
            invalid.append(path)

    if not invalid:
        print("Blade whitespace check passed.")
        return 0

    print("Blade whitespace issues found:")

    for path in invalid:
        print(f"- {path}")

    print()
    print("Jalankan: npm run format:blade")

    return 1


def main() -> int:
    parser = argparse.ArgumentParser(
        description=(
            "Cleanup whitespace Blade yang tidak ditangani "
            "oleh Prettier."
        )
    )

    parser.add_argument(
        "--check",
        action="store_true",
        help=(
            "Hanya memeriksa. Tidak menulis file dan exit 1 "
            "jika ditemukan whitespace yang belum bersih."
        ),
    )

    args = parser.parse_args()
    files = blade_files()

    if not files:
        print("Tidak ada file Blade ditemukan.")
        return 0

    if args.check:
        return run_check(files)

    return run_write(files)


if __name__ == "__main__":
    sys.exit(main())
