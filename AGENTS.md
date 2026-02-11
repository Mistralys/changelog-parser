# AGENTS.md - AI Collaboration Operating System
# Changelog Parser Project

> **Core Principle**: The Project Manifest is the authoritative source of truth. If code contradicts the manifest, the code is likely wrong.

---

## 📚 Project Manifest - Your Primary Resource

### 🎯 Location
**Absolute Path**: `/docs/agents/project-manifest/`

All AI agents **MUST** consult the manifest before reading implementation code. This minimizes token waste and ensures architectural integrity.

### 📖 Manifest Documents (Read in Order)

| Document | Purpose | When to Consult |
|----------|---------|-----------------|
| **[README.md](docs/agents/project-manifest/README.md)** | Project overview, features, and quick navigation | First contact with codebase |
| **[tech-stack.md](docs/agents/project-manifest/tech-stack.md)** | PHP 8.4, dependencies, architectural patterns | Understanding design decisions |
| **[constraints.md](docs/agents/project-manifest/constraints.md)** | Coding standards, naming conventions, error handling rules | Before writing/modifying ANY code |
| **[public-api.md](docs/agents/project-manifest/public-api.md)** | Method signatures, class interfaces, public contracts | Understanding how to use classes |
| **[file-tree.md](docs/agents/project-manifest/file-tree.md)** | Directory structure with descriptions | Locating files or understanding organization |
| **[data-flows.md](docs/agents/project-manifest/data-flows.md)** | How components interact, processing pipelines | Understanding system behavior |

---

## 🚀 Quick Start Workflow - New Agent Onboarding

**Follow this exact sequence for optimal efficiency:**

```
┌─────────────────────────────────────────────────────────────┐
│ STEP 1: Read Manifest README                                │
│ Purpose: Get high-level understanding                       │
│ File: docs/agents/project-manifest/README.md                │
│ Time: 30 seconds                                            │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ STEP 2: Internalize Tech Stack                              │
│ Purpose: Understand PHP 8.4, patterns, dependencies         │
│ File: docs/agents/project-manifest/tech-stack.md            │
│ Focus: Reader Pattern, OperationResult, Type Safety         │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ STEP 3: Absorb Constraints (CRITICAL)                       │
│ Purpose: Learn non-negotiable rules                         │
│ File: docs/agents/project-manifest/constraints.md           │
│ Key Areas: Strict types, immutability, naming conventions   │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ STEP 4: Reference Public API                                │
│ Purpose: Know available methods without reading code        │
│ File: docs/agents/project-manifest/public-api.md            │
│ Use Case: "What methods does ChangelogParser expose?"       │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ STEP 5: Consult File Tree (As Needed)                       │
│ Purpose: Locate specific files                              │
│ File: docs/agents/project-manifest/file-tree.md             │
│ Use Case: "Where is the Markdown reader implementation?"    │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ STEP 6: Read Implementation Code (Last Resort)              │
│ Purpose: Only when manifest doesn't answer the question     │
│ Location: src/ directory                                    │
└─────────────────────────────────────────────────────────────┘
```

**Total Onboarding Time**: 5-7 minutes of focused reading

---

## 📝 Manifest Maintenance Rules

**CRITICAL**: When you modify code, you MUST update the corresponding manifest documents. Stale documentation is worse than no documentation.

### Change → Document Mapping Table

| Code Change | Manifest Files to Update | Update Type |
|-------------|-------------------------|-------------|
| **Add new public method** | `public-api.md` | Add signature + description |
| **Add new class** | `file-tree.md`, `public-api.md` | Add file entry + API section |
| **Change method signature** | `public-api.md` | Update signature |
| **Add new Reader implementation** | `tech-stack.md`, `file-tree.md`, `public-api.md` | Add pattern example + file + API |
| **Add new Change type** | `tech-stack.md`, `file-tree.md`, `public-api.md` | Add to hierarchy + file + API |
| **Modify error handling** | `constraints.md`, `tech-stack.md` | Update error handling rules |
| **Add new dependency** | `tech-stack.md` | Add to dependencies section |
| **Change architectural pattern** | `tech-stack.md`, `data-flows.md` | Update pattern + flow diagram |
| **Add new test suite** | `file-tree.md` | Add to test suites table |
| **Modify coding standard** | `constraints.md` | Update or add rule with rationale |

### Manifest Update Checklist

Before marking work complete, verify:

- [ ] All modified public methods reflected in `public-api.md`
- [ ] New files added to `file-tree.md` with descriptions
- [ ] Architectural changes documented in `tech-stack.md`
- [ ] New constraints/rules added to `constraints.md`
- [ ] Data flow changes updated in `data-flows.md`
- [ ] README.md timestamp updated (`Last Updated: [date]`)

---

## ⚡ Efficiency Rules - Search Smart, Not Hard

### Rule 1: Manifest Before Code
**ALWAYS check manifest documents before reading source files.**

❌ **Bad Workflow**:
```
Agent: "Let me read src/ChangelogParser.php to find all public methods..."
[Wastes 500+ tokens reading entire file]
```

✅ **Good Workflow**:
```
Agent: "Let me check public-api.md for ChangelogParser methods..."
[Finds answer in 50 tokens]
```

### Rule 2: File Location Lookup Protocol

**Question**: "Where is the JSON reader implementation?"

**Required Steps**:
1. Check `file-tree.md` → `src/Reader/JSONChangelogReader.php`
2. Only then read the file if implementation details are needed

**Do NOT**:
- Use `file_search` tool as first resort
- Grep the entire codebase
- Read multiple files hoping to find it

### Rule 3: API Usage Lookup Protocol

**Question**: "How do I get the latest version from a parser?"

**Required Steps**:
1. Check `public-api.md` → Find `ChangelogParser::getLatestVersion()` signature
2. If behavior unclear, check `data-flows.md`
3. Only then read implementation if logic details needed

### Rule 4: Understanding Patterns

**Question**: "How does error handling work?"

**Required Steps**:
1. Check `tech-stack.md` → "Error Handling" section
2. Check `constraints.md` → "Exception vs OperationResult"
3. Only then look at implementation examples

### Token Efficiency Metrics

| Approach | Avg Tokens | Success Rate |
|----------|-----------|--------------|
| **Manifest-First** | 50-200 | 95% |
| **Code-First** | 500-2000 | 70% |
| **Grep-Heavy** | 300-1000 | 60% |

---

## 🚨 Failure Protocol & Decision Matrix

### Ambiguity Resolution

| Scenario | Decision Protocol | Priority |
|----------|------------------|----------|
| **Manifest conflicts with code** | Trust manifest. Flag code for review. Document discrepancy. | MUST |
| **Ambiguous requirement from user** | Use most restrictive interpretation per `constraints.md`. Ask clarifying questions if critical. | MUST |
| **Multiple valid approaches** | Choose approach matching existing patterns in `tech-stack.md`. | SHOULD |
| **Undocumented edge case** | Handle defensively. Add test case. Document in constraints. | SHOULD |
| **Missing manifest information** | Document finding in appropriate manifest file. Proceed with safest assumption. | SHOULD |
| **Unclear error handling strategy** | Use OperationResult for expected failures, exceptions for programming errors (per constraints). | MUST |

### Specific Project Decision Rules

#### Type Declaration Conflicts
**Scenario**: Code lacks `declare(strict_types=1);`

**Action**: 
1. Add the declaration immediately
2. Priority: CRITICAL (violates constraints)

#### Method Naming Inconsistencies
**Scenario**: New method doesn't follow `get*()`, `is*()`, or `require*()` convention

**Action**:
1. Rename to match constraints
2. Update `public-api.md`
3. Priority: HIGH

#### Reader Pattern Violations
**Scenario**: Reader class has business logic (filtering, comparison)

**Action**:
1. Move logic to `ChangelogParser`
2. Keep reader focused on parsing only
3. Priority: CRITICAL (architectural violation)

#### Immutability Violations
**Scenario**: Request to add setter to `ChangelogVersion` or change entries

**Action**:
1. Refuse. Explain immutability principle from constraints
2. Suggest alternative (e.g., create new instance)
3. Priority: CRITICAL

#### Missing Error Codes
**Scenario**: New exception thrown without unique error code constant

**Action**:
1. Add `ERROR_*` constant following numbering scheme
2. Update exception instantiation
3. Priority: HIGH

### When to Escalate (Ask User)

**ASK when**:
- Requirement contradicts established architectural pattern
- Request would require breaking backward compatibility
- Multiple valid implementations exist with different trade-offs
- User intent is genuinely ambiguous despite manifest consultation

**DO NOT ASK when**:
- Constraint violation is clear (just fix it)
- Pattern is documented in tech-stack.md (follow it)
- File location is in file-tree.md (just read it)
- Naming convention is in constraints.md (apply it)

---

## 📊 Project Statistics

### Technology Profile
- **Language**: PHP 8.4+
- **Type System**: Strict type declarations throughout
- **Package Manager**: Composer
- **Dependencies**: application-utils, version-parser
- **Testing**: PHPUnit + PHPStan

### Architecture Profile
- **Pattern**: Reader Pattern + Operation Result Pattern
- **Design**: Type-safe hierarchies, value objects, factory methods
- **Paradigm**: Immutable data models, defensive programming
- **Error Strategy**: OperationResult for validation, exceptions for programming errors

### Codebase Statistics
- **Source Files**: ~10 core classes
- **Test Suites**: 4 focused test suites
- **Lines of Code**: Small, focused library
- **Public API Surface**: Documented in public-api.md

### Cognitive Complexity
- **Onboarding Time**: 5-7 minutes (manifest-first approach)
- **Modification Risk**: Low (strong type safety + immutability)
- **Test Coverage**: High (multiple test dimensions)

---

## 🎯 Success Criteria for Agent Sessions

An agent session is successful when:

✅ **Zero Constraint Violations**: All code follows rules in `constraints.md`  
✅ **Manifest Sync**: Any code changes reflected in appropriate manifest docs  
✅ **Type Safety Maintained**: Strict types, proper hints, no weakening  
✅ **Pattern Consistency**: New code follows existing architectural patterns  
✅ **Test Coverage**: New features have corresponding test cases  
✅ **Documentation Complete**: Public API changes documented  

---

## 🔄 Iterative Improvement

This AGENTS.md file is a living document.

**When to Update This File**:
- Recurring agent confusion → Add to decision matrix
- New architectural pattern → Add to workflow guidance
- Manifest structure changes → Update file paths
- New critical constraint → Add to failure protocol

**Update Protocol**:
1. Identify recurring issue or ambiguity
2. Document clear decision rule
3. Update relevant section
4. Increment version number (in project-manifest/README.md)

---

## 📞 Getting Help

| Issue Type | Resource |
|------------|----------|
| API usage questions | `public-api.md` |
| "Where is X?" questions | `file-tree.md` |
| "How do I...?" questions | `data-flows.md` |
| "Can I...?" questions | `constraints.md` |
| "Why is it designed...?" | `tech-stack.md` |
| All else | Read source code in `src/` |

---

**Agent Version**: 1.0  
**Last Updated**: February 11, 2026  
**Manifest Compatibility**: 1.0+
