# -*- coding: utf-8 -*-
import os
import re
import glob

def get_registry_tools():
    with open('includes/tools_registry.php', 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Extract each tool definition array
    # e.g. 'net-salary-calculator' => [ ... ]
    pattern = r"'([a-z0-9\-]+)'\s*=>\s*\[\s*'id'\s*=>\s*(\d+),\s*'slug'\s*=>\s*'([a-z0-9\-]+)',\s*'name'\s*=>\s*'([^']+)',\s*'description'\s*=>\s*'([^']+)',\s*'category'\s*=>\s*'([^']+)',\s*'icon'\s*=>\s*'([^']+)'"
    tools = []
    for match in re.finditer(pattern, content):
        tools.append({
            'key': match.group(1),
            'id': match.group(2),
            'slug': match.group(3),
            'name': match.group(4),
            'description': match.group(5),
            'category': match.group(6),
            'icon': match.group(7)
        })
    return tools

def check_status():
    tools = get_registry_tools()
    print(f"Total tools in registry: {len(tools)}")
    
    existing_files = {os.path.splitext(os.path.basename(p))[0] for p in glob.glob('tools/*.php')}
    print(f"Existing files in tools/: {len(existing_files)}")
    
    missing = [t for t in tools if t['slug'] not in existing_files]
    print(f"Missing tool files: {len(missing)}")
    
    by_category = {}
    for t in missing:
        cat = t['category']
        by_category.setdefault(cat, []).append(t)
        
    print("\nMissing by category:")
    for cat, items in by_category.items():
        print(f" - {cat}: {len(items)}")

if __name__ == '__main__':
    check_status()
