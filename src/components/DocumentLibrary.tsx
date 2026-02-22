'use client';

import { useState } from 'react';
import { useAuth } from '@/components/AuthProvider';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import {
  FileText,
  File,
  FileSpreadsheet,
  FileImage,
  Download,
  Eye,
  Search,
  FolderOpen,
  Upload,
  Filter,
  Clock,
  User,
  MoreVertical,
} from 'lucide-react';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

// Document categories
const categories = [
  { id: 'all', label: 'All Documents', icon: FolderOpen },
  { id: 'legal', label: 'Legal', icon: FileText },
  { id: 'financial', label: 'Financial', icon: FileSpreadsheet },
  { id: 'due-diligence', label: 'Due Diligence', icon: File },
  { id: 'marketing', label: 'Marketing', icon: FileImage },
  { id: 'contracts', label: 'Contracts', icon: FileText },
];

// Sample documents (in a real app, these would come from an API)
const sampleDocuments = [
  {
    id: '1',
    name: 'Bond Prospectus Draft v2.3',
    category: 'legal',
    type: 'pdf',
    size: '2.4 MB',
    uploadedBy: 'Sarah Johnson',
    uploadedAt: '2026-02-18',
    version: '2.3',
  },
  {
    id: '2',
    name: 'Financial Statements Q4 2025',
    category: 'financial',
    type: 'xlsx',
    size: '1.8 MB',
    uploadedBy: 'Michael Chen',
    uploadedAt: '2026-02-15',
    version: '1.0',
  },
  {
    id: '3',
    name: 'Due Diligence Checklist',
    category: 'due-diligence',
    type: 'pdf',
    size: '456 KB',
    uploadedBy: 'Admin User',
    uploadedAt: '2026-02-10',
    version: '3.1',
  },
  {
    id: '4',
    name: 'Investor Presentation',
    category: 'marketing',
    type: 'pptx',
    size: '8.2 MB',
    uploadedBy: 'Sarah Johnson',
    uploadedAt: '2026-02-12',
    version: '1.2',
  },
  {
    id: '5',
    name: 'Term Sheet - Final',
    category: 'contracts',
    type: 'pdf',
    size: '890 KB',
    uploadedBy: 'Admin User',
    uploadedAt: '2026-02-08',
    version: '1.0',
  },
  {
    id: '6',
    name: 'Credit Rating Application',
    category: 'financial',
    type: 'pdf',
    size: '3.1 MB',
    uploadedBy: 'Michael Chen',
    uploadedAt: '2026-02-05',
    version: '1.0',
  },
  {
    id: '7',
    name: 'Legal Opinion Letter',
    category: 'legal',
    type: 'docx',
    size: '245 KB',
    uploadedBy: 'Sarah Johnson',
    uploadedAt: '2026-02-01',
    version: '1.0',
  },
  {
    id: '8',
    name: 'KYC Documentation Pack',
    category: 'due-diligence',
    type: 'pdf',
    size: '5.6 MB',
    uploadedBy: 'Admin User',
    uploadedAt: '2026-01-28',
    version: '2.0',
  },
];

const getFileIcon = (type: string) => {
  switch (type) {
    case 'pdf':
      return <FileText className="h-5 w-5 text-red-500" />;
    case 'xlsx':
    case 'xls':
      return <FileSpreadsheet className="h-5 w-5 text-green-600" />;
    case 'docx':
    case 'doc':
      return <FileText className="h-5 w-5 text-blue-500" />;
    case 'pptx':
    case 'ppt':
      return <FileImage className="h-5 w-5 text-orange-500" />;
    default:
      return <File className="h-5 w-5 text-stone-500" />;
  }
};

export function DocumentLibrary() {
  const { user, currentProject } = useAuth();
  const [searchQuery, setSearchQuery] = useState('');
  const [activeCategory, setActiveCategory] = useState('all');

  // Filter documents
  const filteredDocuments = sampleDocuments.filter((doc) => {
    const matchesSearch = doc.name.toLowerCase().includes(searchQuery.toLowerCase());
    const matchesCategory = activeCategory === 'all' || doc.category === activeCategory;
    return matchesSearch && matchesCategory;
  });

  // Group documents by category for stats
  const documentStats = categories.slice(1).map((cat) => ({
    ...cat,
    count: sampleDocuments.filter((d) => d.category === cat.id).length,
  }));

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl md:text-3xl font-bold text-stone-800 dark:text-white">
            Document Library
          </h1>
          <p className="text-stone-500 dark:text-stone-400 mt-1">
            Access and manage deal documents for {currentProject?.name || 'your project'}
          </p>
        </div>
        <Button className="bg-teal-600 hover:bg-teal-700 text-white">
          <Upload className="h-4 w-4 mr-2" />
          Upload Document
        </Button>
      </div>

      {/* Category Stats */}
      <div className="grid grid-cols-2 md:grid-cols-5 gap-4">
        {documentStats.map((cat) => {
          const Icon = cat.icon;
          return (
            <Card
              key={cat.id}
              className={`bg-white dark:bg-stone-800 border-stone-200 dark:border-stone-700 cursor-pointer transition-all ${
                activeCategory === cat.id ? 'ring-2 ring-teal-500' : 'hover:shadow-md'
              }`}
              onClick={() => setActiveCategory(cat.id)}
            >
              <CardContent className="pt-4 pb-3">
                <div className="flex items-center gap-3">
                  <div className="p-2 rounded-lg bg-stone-100 dark:bg-stone-700">
                    <Icon className="h-4 w-4 text-stone-600 dark:text-stone-300" />
                  </div>
                  <div>
                    <p className="text-xl font-bold text-stone-800 dark:text-white">{cat.count}</p>
                    <p className="text-xs text-stone-500 dark:text-stone-400">{cat.label}</p>
                  </div>
                </div>
              </CardContent>
            </Card>
          );
        })}
      </div>

      {/* Search and Filter */}
      <Card className="bg-white dark:bg-stone-800 border-stone-200 dark:border-stone-700">
        <CardContent className="pt-4">
          <div className="flex flex-col md:flex-row gap-4">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-stone-400" />
              <Input
                placeholder="Search documents..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="pl-10 bg-stone-50 dark:bg-stone-900 border-stone-200 dark:border-stone-700"
              />
            </div>
            <Tabs value={activeCategory} onValueChange={setActiveCategory} className="w-full md:w-auto">
              <TabsList className="grid grid-cols-3 md:flex bg-stone-100 dark:bg-stone-900">
                <TabsTrigger value="all" className="text-xs md:text-sm">All</TabsTrigger>
                <TabsTrigger value="legal" className="text-xs md:text-sm">Legal</TabsTrigger>
                <TabsTrigger value="financial" className="text-xs md:text-sm">Financial</TabsTrigger>
              </TabsList>
            </Tabs>
          </div>
        </CardContent>
      </Card>

      {/* Document List */}
      <Card className="bg-white dark:bg-stone-800 border-stone-200 dark:border-stone-700">
        <CardHeader>
          <div className="flex items-center justify-between">
            <CardTitle className="text-stone-800 dark:text-white">
              {activeCategory === 'all' ? 'All Documents' : categories.find(c => c.id === activeCategory)?.label}
            </CardTitle>
            <Badge variant="secondary" className="bg-stone-100 dark:bg-stone-700 text-stone-600 dark:text-stone-300">
              {filteredDocuments.length} document{filteredDocuments.length !== 1 ? 's' : ''}
            </Badge>
          </div>
        </CardHeader>
        <CardContent>
          {filteredDocuments.length === 0 ? (
            <div className="text-center py-12">
              <FolderOpen className="h-12 w-12 text-stone-300 dark:text-stone-600 mx-auto mb-4" />
              <p className="text-stone-500 dark:text-stone-400">No documents found</p>
              <p className="text-sm text-stone-400 dark:text-stone-500 mt-1">
                Try adjusting your search or filter
              </p>
            </div>
          ) : (
            <div className="space-y-2">
              {filteredDocuments.map((doc) => (
                <div
                  key={doc.id}
                  className="flex items-center gap-4 p-4 rounded-lg bg-stone-50 dark:bg-stone-700/50 hover:bg-stone-100 dark:hover:bg-stone-700 transition-colors group"
                >
                  {/* File Icon */}
                  <div className="p-2 rounded-lg bg-white dark:bg-stone-800 shadow-sm">
                    {getFileIcon(doc.type)}
                  </div>

                  {/* File Info */}
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center gap-2">
                      <h3 className="font-medium text-stone-800 dark:text-white truncate">
                        {doc.name}
                      </h3>
                      <Badge variant="outline" className="text-xs">
                        v{doc.version}
                      </Badge>
                    </div>
                    <div className="flex items-center gap-4 mt-1 text-sm text-stone-500 dark:text-stone-400">
                      <span className="flex items-center gap-1">
                        <User className="h-3 w-3" />
                        {doc.uploadedBy}
                      </span>
                      <span className="flex items-center gap-1">
                        <Clock className="h-3 w-3" />
                        {new Date(doc.uploadedAt).toLocaleDateString('en-US', {
                          month: 'short',
                          day: 'numeric',
                          year: 'numeric',
                        })}
                      </span>
                      <span className="uppercase text-xs font-medium">{doc.type}</span>
                      <span>{doc.size}</span>
                    </div>
                  </div>

                  {/* Actions */}
                  <div className="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <Button variant="ghost" size="sm" className="text-stone-600 dark:text-stone-300">
                      <Eye className="h-4 w-4" />
                    </Button>
                    <Button variant="ghost" size="sm" className="text-stone-600 dark:text-stone-300">
                      <Download className="h-4 w-4" />
                    </Button>
                    <DropdownMenu>
                      <DropdownMenuTrigger asChild>
                        <Button variant="ghost" size="sm" className="text-stone-600 dark:text-stone-300">
                          <MoreVertical className="h-4 w-4" />
                        </Button>
                      </DropdownMenuTrigger>
                      <DropdownMenuContent align="end" className="bg-white dark:bg-stone-800 border-stone-200 dark:border-stone-700">
                        <DropdownMenuItem className="text-stone-700 dark:text-stone-300">
                          View Details
                        </DropdownMenuItem>
                        <DropdownMenuItem className="text-stone-700 dark:text-stone-300">
                          Download
                        </DropdownMenuItem>
                        <DropdownMenuItem className="text-stone-700 dark:text-stone-300">
                          Share
                        </DropdownMenuItem>
                        <DropdownMenuItem className="text-stone-700 dark:text-stone-300">
                          Version History
                        </DropdownMenuItem>
                      </DropdownMenuContent>
                    </DropdownMenu>
                  </div>
                </div>
              ))}
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
